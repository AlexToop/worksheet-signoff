<?php

$context = "demonstrator";
include_once '../includes/Security.php';
include_once 'DatabaseDAO.php';
include_once 'DatabaseQueriesHelper.php';

$dao = new DatabaseDAO();

if(isset($_POST['addUserKey'])) {
    addUser($dao, $_POST['type'], $_POST['username'], $_POST['firstname'], $_POST['lastname'], $_POST['classid']);

} elseif(isset($_POST['removeUserKey'])) {
    removeUser($dao, $_POST['userid'], $_POST['typeNo'], $_POST['classid']);

} elseif(isset($_POST['returnAllUsersKey'])) {
    echo getUsers($dao, $_POST['classIdForUsers'], $_POST['groupForUsers']);

} elseif(isset($_POST['getClassExportDataKey'])) {
    echo getClassExportData($dao, $_POST['classid'], false, false);

} elseif(isset($_POST['sendWorksheetSignOffKey'])) {
    sendWorksheetSignOff($dao, $_POST['submission']);

} elseif(isset($_POST['deleteModuleKey'])) {
    deleteModule($dao, $_POST['moduleToDelete']);

} elseif(isset($_POST['createNewModuleKey'])) {
    createNewModule($dao, $_POST['addModule'], $_POST['students'], $_POST['startyear']);

} elseif(isset($_POST['getPercentagesExportDataKey'])) {
    echo getClassExportData($dao, $_POST['classid'], true, true);

}


////////////////////////////// main functions used //////////////////////////////////////

/**
 * Removes a user from the appropriate class(/s) group
 *
 * @param $dao
 * @param $userid
 * @param $typeNo
 * @param $classid
 */
function removeUser($dao, $userid, $typeNo, $classid) {
    if(strtolower($_SESSION['username']) == strtolower($userid)) {
        echo "fail";
    } else {
        if($typeNo == 4) {
            $classIds = $dao->getClassesForAdmin();
            for($classIndex = 0; $classIndex < sizeof($classIds); $classIndex++) {
                echo ($dao->removeUserFromClass($classIds[$classIndex]['classid'], $userid) === false) ? "fail" : "";
            }
        } else {
            echo ($dao->removeUserFromClass($classid, $userid) === false) ? "fail" : "done";
        }
    }
}


/**
 * Adds a user to a class(/s) group
 *
 * @param $dao
 * @param $typeId
 * @param $userid
 * @param $firstname
 * @param $lastname
 * @param $classid
 */
function addUser($dao, $typeId, $userid, $firstname, $lastname, $classid) {
    if($typeId == 4) {
        $dao->addUserDetails($userid, $firstname, $lastname);
        $classIds = $dao->getClassesForAdmin();
        for($classIndex = 0; $classIndex < sizeof($classIds); $classIndex++) {
            echo ($dao->addUserToModule($classIds[$classIndex]['classid'], $userid, $typeId) === false) ? "fail" : "";
        }
    } else {
        $dao->addUserDetails($userid, $firstname, $lastname);
        echo ($dao->addUserToModule($classid, $userid, $typeId) === false) ? "fail" : "done";
    }
}


/**
 * Returns the a json class structure
 *
 * @param $dao
 * @param $classid
 * @param $isPercentageRequest
 * @param $isLockdateRequest
 * @return false|string
 */
function getClassExportData($dao, $classid, $isPercentageRequest, $isLockdateRequest) {
    $returnArray = [];
    $classStructure = $dao->getStructureOfClassId($classid);
    $classStructure = json_decode($classStructure);
    array_push($returnArray, getColumnRow($classStructure, $classid, $isLockdateRequest));
    $students = $dao->getUsersInModule($classid, 1);
    foreach($students as $key => $studentid) {
        array_push($returnArray, getStudentRow($dao, $studentid, $classStructure, $classid, $isPercentageRequest));
    }
    // json_encode will return null if invalid data
    return (json_encode($returnArray)) ? json_encode($returnArray) : "fail";
}


/**
 * Deletes a module from the dao
 *
 * @param $dao
 * @param $classid
 */
function deleteModule($dao, $classid) {
    if($classid == 1) {
        echo "fail";
    } else {
        echo ($dao->deleteClass($classid) === false) ? "fail" : "done";
    }
}


/**
 * Returns all users of a group in a class
 *
 * @param $dao
 * @param $classId
 * @param $groupId
 * @return false|string
 */
function getUsers($dao, $classId, $groupId) {
    $users = $dao->getUsers($classId, $groupId);

    if($users === false) {
        return "fail";
    } else {
        $encodedUsers = json_encode($users);
        return ($encodedUsers) ? $encodedUsers : "fail";
    }
}


/**
 * Sends a worksheet sign-off mark after checking the data is valid against HTML exploits
 *
 * @param $dao
 * @param $submissionDetails
 */
function sendWorksheetSignOff($dao, $submissionDetails) {
    $submissionArray = explode("!", $submissionDetails);
    // It is valid if $lockedWorksheets is null. This means there are no locked worksheets.
    $lockedWorksheets = json_decode($dao->getLockedWorksheetsArray($_SESSION['classid']));

    // Checks no html edit exploit to avoid locked worksheets has been used, is fine if $lockedWorksheets is null
    if(in_array($submissionArray[0], $lockedWorksheets)) {
        echo "fail";
    } else {
        // Checks no html edit exploit to edit maximum or minimum marks has been used
        $submissionArray[3] = ($submissionArray[3] > 100) ? 100 : $submissionArray[3];
        $submissionArray[3] = ($submissionArray[3] < 0) ? 0 : $submissionArray[3];
        echo ($dao->setWorksheetCompletionForUserId($_SESSION['signOffStudent'], $_SESSION['classid'],
                $submissionArray[0], $submissionArray[1], $submissionArray[2], $submissionArray[3]) === false) ?
            "fail" :
            "done";
    }
}


/**
 * Creates a new module given the information supplied. Cascading checking is used as multiple database entries are
 * required.
 *
 * @param $dao
 * @param $worksheetDetails
 * @param $uploadedStudentsData
 * @param $startYearDropDownSelection
 */
function createNewModule($dao, $worksheetDetails, $uploadedStudentsData, $startYearDropDownSelection) {
    $startYear = ($startYearDropDownSelection == "selection1") ? (date("Y") - 1) : date("Y");

    if(($dao->createNewClassRecord($worksheetDetails[0], $startYear)) === false) {
        echo "fail";
    } else {
        $classid = $dao->getClassidForModuleDetails($worksheetDetails[0], $startYear);

        if($classid === false) {
            echo "fail";
        } else {
            if(addRequiredManagementUsers($dao, $classid) === true) {
                if(addWorksheets($dao, $classid, $worksheetDetails)) {

                    // Removes lecturers from the temp module if they are placed there currently. If this
                    // fails it should not effect the overall add module procedure.
                    if(($_SESSION['module'] == "class-placeholder") && ($_SESSION['usergroup'] !== "admin")) {
                        $dao->removeUserFromClass($_SESSION['classid'], $_SESSION['username']);
                    }

                    // attempts to add users. If they already exist in the system, we don't mind so won't catch errors.
                    addStudentsGeneralInformation($dao, $uploadedStudentsData);
                    echo (addStudentsToModuleClass($dao, $uploadedStudentsData, $classid) === false) ? "fail" : "done";
                } else {
                    echo "fail";
                }
            } else {
                echo "fail";
            }
        }
    }
}


$dao->closeConnection();
?>
