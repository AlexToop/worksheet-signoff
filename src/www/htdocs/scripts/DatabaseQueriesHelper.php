<?php

/**
 * Adds user entries given the array of given students data
 *
 * @param $dao
 * @param $givenStudents
 */
function addStudentsGeneralInformation($dao, $givenStudents) {
    $indexUserFirstLast = getIndexUserFirstLast($givenStudents);
    $usernameIndex = $indexUserFirstLast[0];
    $firstnameIndex = $indexUserFirstLast[1];
    $lastnameIndex = $indexUserFirstLast[2];

    // start at index 1 due to column names
    for($studentNo = 1; $studentNo < sizeof($givenStudents); $studentNo++) {
        // attempts to add users to the users store. If they already exist there, we don't mind so won't catch errors.
        $dao->addUserDetails($givenStudents[$studentNo][$usernameIndex], $givenStudents[$studentNo][$firstnameIndex], $givenStudents[$studentNo][$lastnameIndex]);
    }
}


/**
 * Adds all admins to a (new) module and add (likely) creating lecturer to the module
 *
 * @param $dao
 * @param $classid
 * @return bool
 */
function addRequiredManagementUsers($dao, $classid) {
    $isSuccessful = true;

    // always required for admin functionality
    $adminArray = $dao->getAdmins();
    for($adminIndex = 0; $adminIndex < sizeof($adminArray); $adminIndex++) {
        if($isSuccessful === true) {
            $username = $adminArray[$adminIndex]['userid'];
            if($dao->addUserToModule($classid, $username, 4) === false) {
                $isSuccessful = false;
            }
        }
    }

    if ($isSuccessful === true){
        if($_SESSION['usergroup'] !== "admin") {
            // adds the person creating this module to the lecturer list
            if ($dao->addUserToModule($classid, $_SESSION['username'], 3) === false) {
                $isSuccessful = false;
            }
        }
    }
   return $isSuccessful;
}


/**
 * Adds all students of an array to a module
 *
 * @param $dao
 * @param $givenStudents
 * @param $classid
 * @return bool
 */
function addStudentsToModuleClass($dao, $givenStudents, $classid) {
    $isSuccessful = true;

    $indexUserFirstLast = getIndexUserFirstLast($givenStudents);
    $usernameIndex = $indexUserFirstLast[0];

    // start at index 1 due to column names
    for($studentNo = 1; $studentNo < sizeof($givenStudents); $studentNo++) {
        if($isSuccessful === true) {
            $username = $givenStudents[$studentNo][$usernameIndex];
            if ($username && $username != ""){
                if($dao->addUserToModule($classid, $username, 1) === false) {
                    $isSuccessful = false;
                }
            }
        }
    }
    return $isSuccessful;
}


/**
 * Adds worksheets to a class in the database
 *
 * @param $dao
 * @param $classid
 * @param $givenData
 * @return bool
 */
function addWorksheets($dao, $classid, $givenData) {
    $isSuccessful = true;

    for($worksheetNo = 1; $worksheetNo < sizeof($givenData); $worksheetNo++) {
        if($isSuccessful === true) {
            $worksheetLockdate = $givenData[$worksheetNo][0];
            if($dao->addWorksheetToClass($classid, $worksheetNo, $worksheetLockdate) === false) {
                $isSuccessful = false;
            } else {
                $isSuccessful = addQuestions($dao, $classid, $worksheetNo, $givenData[$worksheetNo][1]);
            }
        }
    }
    return $isSuccessful;
}


/**
 * Adds questions to a worksheet in a class in the database
 *
 * @param $dao
 * @param $classid
 * @param $worksheetNo
 * @param $questionInfo
 * @return bool
 */
function addQuestions($dao, $classid, $worksheetNo, $questionInfo) {
    $isSuccessful = true;
    for($questionNo = 1; $questionNo <= sizeof($questionInfo); $questionNo++) {
        if($isSuccessful === true) {
            if($dao->addQuestionToWorksheet($classid, $worksheetNo, $questionNo) === false) {
                $isSuccessful = false;
            } else {
                $isSuccessful = addParts($dao, $classid, $worksheetNo, $questionNo, $questionInfo[$questionNo - 1]);
            }
        }
    }
    return $isSuccessful;
}


/**
 * Adds parts to a question in a worksheet in a class in the database
 *
 * @param $dao
 * @param $classid
 * @param $worksheetNo
 * @param $questionno
 * @param $questionArray
 * @return bool
 */
function addParts($dao, $classid, $worksheetNo, $questionno, $questionArray) {
    $isSuccessful = true;
    for($partNo = 1; $partNo <= sizeof($questionArray); $partNo++) {
        if($isSuccessful === true) {
            $partWeight = $questionArray[$partNo - 1];
            if($dao->addPartToQuestion($classid, $worksheetNo, $questionno, $partNo, $partWeight) === false) {
                $isSuccessful = false;
            }
        }
    }
    return $isSuccessful;
}


/**
 * Returns an array of the indexes relevant for student data, the username, firstname and lastname indexes.
 *
 * @param $fileArrayStudentsData
 * @return array
 */
function getIndexUserFirstLast($fileArrayStudentsData) {
    $indexOfUsername = null;
    $indexOfFirstName = null;
    $indexOfLastName = null;

    for($indexNo = 0; $indexNo < sizeof($fileArrayStudentsData[0]); $indexNo++) {
        if(strcasecmp($fileArrayStudentsData[0][$indexNo], 'Username') == 0) {
            $indexOfUsername = $indexNo;
        } elseif(strcasecmp($fileArrayStudentsData[0][$indexNo], 'First Name') == 0) {
            $indexOfFirstName = $indexNo;
        } elseif(strcasecmp($fileArrayStudentsData[0][$indexNo], 'Last Name') == 0) {
            $indexOfLastName = $indexNo;
        }
    }

    $returnArray = [$indexOfUsername, $indexOfFirstName, $indexOfLastName];
    return $returnArray;
}


/**
 * Obtains the row of marks for a student in a class
 *
 * @param $dao
 * @param $studentid
 * @param $classStructure
 * @param $classid
 * @param $isPercentageRequest
 * @return array
 */
function getStudentRow($dao, $studentid, $classStructure, $classid, $isPercentageRequest) {
    $studentDetails = $dao->getUsersDetails($studentid);
    $row = [];
    array_push($row, $studentDetails['lastname']);
    array_push($row, $studentDetails['firstname']);
    array_push($row, $studentDetails['userid']);

    for($currentWorksheet = 0; $currentWorksheet < sizeof($classStructure); $currentWorksheet++) {
        if(sizeof($classStructure[$currentWorksheet]) > 1) {
            for($currentQuestion = 0; $currentQuestion < sizeof($classStructure[$currentWorksheet]); $currentQuestion++) {
                if($classStructure[$currentWorksheet][$currentQuestion] > 1) {
                    for($currPart = 1; $currPart <= $classStructure[$currentWorksheet][$currentQuestion]; $currPart++) {
                        array_push($row, getMarkForStudent($dao, $studentid, $classid, ($currentWorksheet + 1), ($currentQuestion + 1), $currPart, $isPercentageRequest));
                    }
                } else {
                    array_push($row, getMarkForStudent($dao, $studentid, $classid, ($currentWorksheet + 1), ($currentQuestion + 1), 1, $isPercentageRequest));
                }
            }
        } else {
            $mark = getMarkForStudent($dao, $studentid, $classid, ($currentWorksheet + 1), 1, 1, $isPercentageRequest);
            array_push($row, $mark);
        }
    }
    return $row;
}


/**
 * Returns the first row for the csv. Contains all relevant column names.
 *
 * @param $classStructure
 * @param $classId
 * @param $isLockdateRequest
 * @return array
 */
function getColumnRow($classStructure, $classId, $isLockdateRequest) {
    $row = ['Last Name', 'First Name', 'Username'];
    $dao = new DatabaseDAO();
    for($currentWorksheet = 0; $currentWorksheet < sizeof($classStructure); $currentWorksheet++) {
        if(sizeof($classStructure[$currentWorksheet]) > 1) {

            for($currentQuestion = 0; $currentQuestion < sizeof($classStructure[$currentWorksheet]); $currentQuestion++) {
                if($classStructure[$currentWorksheet][$currentQuestion] > 1) {

                    for($currPart = 1; $currPart <= $classStructure[$currentWorksheet][$currentQuestion]; $currPart++) {
                        $ansWeight = $dao->getAnswerWeight($classId, ($currentWorksheet + 1), ($currentQuestion + 1), $currPart)['answerweight'];
                        $ansWeight = " [Total Pts: " . $ansWeight . " Score]";
                        if ($isLockdateRequest){
                            $date = $dao->getLockdate($classId, ($currentWorksheet + 1));
                            $ansWeight .= ($date) ? (" Lock-date: " . $date) : "";
                        }
                        array_push($row, "W" . ($currentWorksheet + 1) . "Q" . ($currentQuestion + 1) . "P" . ($currPart) . $ansWeight);
                    }
                } else {
                    $ansWeight = $dao->getAnswerWeight($classId, ($currentWorksheet + 1), ($currentQuestion + 1), 1)['answerweight'];
                    $ansWeight = " [Total Pts: " . $ansWeight . " Score]";
                    if ($isLockdateRequest){
                        $date = $dao->getLockdate($classId, ($currentWorksheet + 1));
                        $ansWeight .= ($date) ? (" Lock-date: " . $date) : "";
                    }
                    array_push($row, "W" . ($currentWorksheet + 1) . "Q" . ($currentQuestion + 1) . $ansWeight);
                }
            }
        } else {
            $ansWeight = $dao->getAnswerWeight($classId, ($currentWorksheet + 1), 1, 1)['answerweight'];
            $ansWeight = " [Total Pts: " . $ansWeight . " Score]";
            if ($isLockdateRequest){
                $date = $dao->getLockdate($classId, ($currentWorksheet + 1));
                $ansWeight .= ($date) ? (" Lock-date: " . $date) : "";
            }
            array_push($row, "W" . ($currentWorksheet + 1) . $ansWeight);
        }
    }
    return $row;
}


/**
 * Gets the recorded mark for a student relevant to the part
 *
 * @param $dao
 * @param $studentId
 * @param $classId
 * @param $worksheetNo
 * @param $questionNo
 * @param $partNo
 * @param $isPercentageRequest
 * @return float|int
 */
function getMarkForStudent($dao, $studentId, $classId, $worksheetNo, $questionNo, $partNo, $isPercentageRequest) {
    $completedWork = json_decode($dao->getCompletedWorkForUserIdInClassId($classId, $studentId), true);
    $ansWeight = $dao->getAnswerWeight($classId, $worksheetNo, $questionNo, $partNo);
    $ansWeight = $ansWeight['answerweight'];
    $percentageAchieved = 0;

    for($currentWorkEntry = 0; $currentWorkEntry < sizeof($completedWork); $currentWorkEntry++) {
        if(!empty($completedWork) && !empty($completedWork[$currentWorkEntry]) &&
            !empty($completedWork[$currentWorkEntry]['percentagedone']) &&
            !empty($completedWork[$currentWorkEntry]['worksheetno']) &&
            !empty($completedWork[$currentWorkEntry]['questionno']) &&
            !empty($completedWork[$currentWorkEntry]['questionpart'])) {

            if($completedWork[$currentWorkEntry]['worksheetno'] == $worksheetNo &&
                $completedWork[$currentWorkEntry]['questionno'] == $questionNo &&
                $completedWork[$currentWorkEntry]['questionpart'] == $partNo) {

                $percentageAchieved = $completedWork[$currentWorkEntry]['percentagedone'];
            }
        }
    }
    return ($isPercentageRequest) ? $percentageAchieved : ($ansWeight * ($percentageAchieved / 100));
}


/**
 * Returns the structure of a class (json)
 *
 * @param $dao
 * @param $classid
 * @return mixed
 */
function getExportStructure($dao, $classid) {
    $structureOfClass = $dao->getStructureOfClassId($classid);
    return $structureOfClass;
}


