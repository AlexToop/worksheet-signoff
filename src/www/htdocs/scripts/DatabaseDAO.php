<?php

class DatabaseDAO {
    private $conn = null;


    /*
     * Gets the connection details for the database. Files are stored in the htdocs_private to increase security.
     */
    function __construct() {
        include_once '../../htdocs_private/data/DBConnection.php';
        $this->conn = get_db_handle();
    }


    /*
     * Ensures that the connection details are reset.
     */
    function closeConnection() {
        $conn = null;
    }


    /*
     * Obtains the classid for the given details.
     *
     * @param string $modulecode
     */
    function getClassidForModuleDetails($modulecode, $startyear) {
        $stmt = $this->conn->prepare("select classid from classes where modulecode = :modulecode and startyear = :startyear");

        $stmt->bindParam(':modulecode', $modulecode);
        $stmt->bindParam(':startyear', $startyear);

        $stmt->execute();
        $returned = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return (sizeof($returned) > 0) ? $returned[0] : null;
    }


    /*
     * Obtains the usergroup of someone given their username and classid.
     *
     * @param string $modulecode
     */
    function getUserGroupForUserInClass($userid, $classid) {
        $exitVal = null;
        $stmt = $this->conn->prepare("select groupid from usergroups where classid = :classid and userid = :userid");

        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':classid', $classid);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if(sizeof($result) != null) {
            $exitVal = $result[0];
        }

        $stmt = null;
        $result = null;
        return $exitVal;
    }


    /**
     * Sets a mark for a user in the database. On conflict it will update the mark
     *
     * @param $givenUserId
     * @param $givenClassId
     * @param $givenWorksheetNo
     * @param $givenQuestionNo
     * @param $givenQuestionPart
     * @param $givenMark
     * @return bool
     */
    function setWorksheetCompletionForUserId($givenUserId, $givenClassId, $givenWorksheetNo, $givenQuestionNo, $givenQuestionPart, $givenMark) {
        $stmt = $this->conn->prepare("insert into SIGNOFFS (USERID, CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART, PERCENTAGEDONE) values (:userid, :classid, :worksheetno, :questionno, :questionpart, :percentagedone) ON CONFLICT (USERID, CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART) DO UPDATE SET percentagedone = EXCLUDED.percentagedone");

        $stmt->bindParam(':userid', $givenUserId);
        $stmt->bindParam(':classid', $givenClassId);
        $stmt->bindParam(':worksheetno', $givenWorksheetNo);
        $stmt->bindParam(':questionno', $givenQuestionNo);
        $stmt->bindParam(':questionpart', $givenQuestionPart);
        $stmt->bindParam(':percentagedone', $givenMark);


        $stmt->execute();

        if($stmt->execute() === false) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Returns a json of the class structure for processing
     *
     * @param $givenClassId
     * @return false|string
     */
    function getStructureOfClassId($givenClassId) {
        $noWorksheets = $this->getNoOfWorksheetsForClassId($givenClassId);

        $questionsOfWks1 = [];
        for($currWorksheetNo = 1; $currWorksheetNo <= $noWorksheets; $currWorksheetNo++) {
            array_push($questionsOfWks1, $this->getNoOfQuestionsForClassIdAndWorksheetNo($givenClassId, $currWorksheetNo));
        }

        $partsOfWks1 = [];
        for($currWorksheetNo = 1; $currWorksheetNo <= $noWorksheets; $currWorksheetNo++) {
            $partsOfWks1[$currWorksheetNo - 1] = [];
            for($currQuestionNo = 1; $currQuestionNo <= $questionsOfWks1[$currWorksheetNo - 1]; $currQuestionNo++) {

                array_push($partsOfWks1[$currWorksheetNo - 1], $this->getNoOfpartsForClassIdAndWorksheetNoAndQuestionNo($givenClassId, $currWorksheetNo, $currQuestionNo));
            }

        }
        return json_encode($partsOfWks1);
    }


    /**
     * Returns the number of worksheets in a class
     *
     * @param $givenClassId
     * @return int
     */
    function getNoOfWorksheetsForClassId($givenClassId) {
        $stmt = $this->conn->prepare("select worksheetno from worksheets where classid = :classid");

        $stmt->bindParam(':classid', $givenClassId);

        $stmt->execute();
        return sizeof($stmt->fetchAll(PDO::FETCH_COLUMN));
    }


    /**
     * Returns the number of questions from the query
     *
     * @param $givenClassId
     * @param $givenWorksheetNo
     * @return int
     */
    function getNoOfQuestionsForClassIdAndWorksheetNo($givenClassId, $givenWorksheetNo) {
        $stmt = $this->conn->prepare("select questionno from questions where classid = :classid and worksheetno = :worksheetno");

        $stmt->bindParam(':classid', $givenClassId);
        $stmt->bindParam(':worksheetno', $givenWorksheetNo);

        $stmt->execute();
        return sizeof($stmt->fetchAll(PDO::FETCH_COLUMN));
    }


    /**
     * Determines and returns the number of parts there are in total for a worksheet question
     *
     * @param $givenClassId
     * @param $givenWorksheetNo
     * @param $givenQuestionNo
     * @return int
     */
    function getNoOfpartsForClassIdAndWorksheetNoAndQuestionNo($givenClassId, $givenWorksheetNo, $givenQuestionNo) {
        $stmt = $this->conn->prepare("select questionPart from parts where classid = :classid and worksheetno = :worksheetno and questionno = :questionno");

        $stmt->bindParam(':classid', $givenClassId);
        $stmt->bindParam(':worksheetno', $givenWorksheetNo);
        $stmt->bindParam(':questionno', $givenQuestionNo);

        $stmt->execute();
        return sizeof($stmt->fetchAll(PDO::FETCH_COLUMN));
    }


    /**
     * Returns all of the work completed by a student in a class in json format for processing
     *
     * @param $givenClassId
     * @param $givenUserId
     * @return false|string
     */
    function getCompletedWorkForUserIdInClassId($givenClassId, $givenUserId) {
        $stmt = $this->conn->prepare("select worksheetno, questionno, questionpart, percentagedone from signoffs where userid = :userid and classid = :classid");

        $stmt->bindParam(':userid', $givenUserId);
        $stmt->bindParam(':classid', $givenClassId);

        $stmt->execute();
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }


    /**
     * Returns 0 or 1 if a student is registered for a class
     *
     * @param $givenClassId
     * @param $givenUserId
     * @return int
     */
    function isStudentRegisteredForClass($givenClassId, $givenUserId) {
        $stmt = $this->conn->prepare("select groupid from usergroups where classid = :classid and userid = :userid");

        $stmt->bindParam(':classid', $givenClassId);
        $stmt->bindParam(':userid', $givenUserId);

        $stmt->execute();
        return sizeof($stmt->fetchAll(PDO::FETCH_COLUMN));
    }


    /**
     * Returns all of the classes in the system
     *
     * @return array
     */
    function getClassesForAdmin() {
        $stmt = $this->conn->prepare("select * from classes");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Creates a new class
     *
     * @param $givenModuleCode
     * @param $givenClassId
     * @return bool
     */
    function createNewClassRecord($givenModuleCode, $givenClassId){
        $stmt = $this->conn->prepare("insert into CLASSES (MODULECODE, STARTYEAR) values (:modulecode, :startyear)");

        $stmt->bindParam(':modulecode', $givenModuleCode);
        $stmt->bindParam(':startyear', $givenClassId);

        return $stmt->execute();
    }


    /**
     * Adds a worksheet with its lock date to a class
     *
     * @param $givenClassId
     * @param $worksheetNo
     * @param $lockDate
     * @return bool
     */
    function addWorksheetToClass($givenClassId, $worksheetNo, $lockDate){
        if ($lockDate == "" || $lockDate == ''){
            $stmt = $this->conn->prepare("insert into WORKSHEETS (CLASSID, WORKSHEETNO, LOCKDATE) values (:classid, :worksheetno, NULL)");
            $stmt->bindParam(':classid', $givenClassId);
            $stmt->bindParam(':worksheetno', $worksheetNo);
            return $stmt->execute();
        }

        $stmt = $this->conn->prepare("insert into WORKSHEETS (CLASSID, WORKSHEETNO, LOCKDATE) values (:classid, :worksheetno, :lockdate)");

        $stmt->bindParam(':classid', $givenClassId);
        $stmt->bindParam(':worksheetno', $worksheetNo);
        $stmt->bindParam(':lockdate', $lockDate);

        return $stmt->execute();
    }


    /**
     * Adds a question to a worksheet in a class
     *
     * @param $givenClassId
     * @param $worksheetNo
     * @param $questionno
     * @return bool
     */
    function addQuestionToWorksheet($givenClassId, $worksheetNo, $questionno){
        $stmt = $this->conn->prepare("insert into QUESTIONS (CLASSID, WORKSHEETNO, QUESTIONNO) values (:classid, :worksheetno, :questionno)");

        $stmt->bindParam(':classid', $givenClassId);
        $stmt->bindParam(':worksheetno', $worksheetNo);
        $stmt->bindParam(':questionno', $questionno);

        return $stmt->execute();
    }


    /**
     * Adds a part to a question in a worksheet in a class
     *
     * @param $givenClassId
     * @param $worksheetNo
     * @param $questionno
     * @param $questionpart
     * @param $answerweight
     * @return bool
     */
    function addPartToQuestion($givenClassId, $worksheetNo, $questionno, $questionpart, $answerweight){
        $stmt = $this->conn->prepare("insert into PARTS (CLASSID, WORKSHEETNO, QUESTIONNO, QUESTIONPART, ANSWERWEIGHT) values (:classid, :worksheetno, :questionno, :questionpart, :answerweight)");

        $stmt->bindParam(':classid', $givenClassId);
        $stmt->bindParam(':worksheetno', $worksheetNo);
        $stmt->bindParam(':questionno', $questionno);
        $stmt->bindParam(':questionpart', $questionpart);
        $stmt->bindParam(':answerweight', $answerweight);

        return $stmt->execute();
    }


    /**
     * Adds a user entry in the database
     *
     * @param $username
     * @param $firstName
     * @param $lastName
     * @return bool
     */
    function addUserDetails($username, $firstName, $lastName){
        $stmt = $this->conn->prepare("insert into USERS (USERID, FIRSTNAME, LASTNAME) values (:userid, :firstname, :lastname)");

        $username = strtolower($username);

        $stmt->bindParam(':userid', $username);
        $stmt->bindParam(':firstname', $firstName);
        $stmt->bindParam(':lastname', $lastName);

        return $stmt->execute();
    }


    /**
     * Adds a userid to a class as a specific user-group
     *
     * @param $classid
     * @param $username
     * @param $usergroup
     * @return bool
     */
    function addUserToModule($classid, $username, $usergroup){
        $stmt = $this->conn->prepare("insert into USERGROUPS (CLASSID, USERID, GROUPID) values (:classid, :username, :usergroup)");

        $username = strtolower($username);

        $stmt->bindParam(':classid', $classid);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':usergroup', $usergroup);

        return $stmt->execute();
    }


    /**
     * Returns all of the classes a user is registered for
     *
     * @param $userid
     * @return array
     */
    function getClassesRegisteredForUser($userid) {
        $stmt = $this->conn->prepare("select classid from usergroups where userid = :userid");

        $stmt->bindParam(':userid', $userid);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    /**
     * Returns the details of a class
     *
     * @param $classid
     * @return array
     */
    function getClassesDetails($classid) {
        $stmt = $this->conn->prepare("select * from classes where classid = :classid");

        $stmt->bindParam(':classid', $classid);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Returns all of the users in a class/module
     *
     * @param $classid
     * @param $groupid
     * @return array
     */
    function getUsersInModule($classid, $groupid) {
        $stmt = $this->conn->prepare("select userid from usergroups where classid = :classid and groupid = :groupid");

        $stmt->bindParam(':classid', $classid);
        $stmt->bindParam(':groupid', $groupid);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    /**
     * Gets the user details of a userid
     *
     * @param $userid
     * @return mixed
     */
    function getUsersDetails($userid) {
        $stmt = $this->conn->prepare("select * from users where userid = :userid");

        $stmt->bindParam(':userid', $userid);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    }


    /**
     * Obtains the weight of a part
     *
     * @param $classid
     * @param $worksheetno
     * @param $questionno
     * @param $questionpart
     * @return mixed
     */
    function getAnswerWeight($classid, $worksheetno, $questionno, $questionpart) {
        $stmt = $this->conn->prepare("select answerweight from parts where classid = :classid and worksheetno = :worksheetno and questionno = :questionno and questionpart = :questionpart");

        $stmt->bindParam(':classid', $classid);
        $stmt->bindParam(':worksheetno', $worksheetno);
        $stmt->bindParam(':questionno', $questionno);
        $stmt->bindParam(':questionpart', $questionpart);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    }


    /**
     * Deletes a class
     *
     * @param $classid
     * @return bool
     */
    function deleteClass($classid) {
        $stmt = $this->conn->prepare("delete from classes where classid = :classid");

        $stmt->bindParam(':classid', $classid);

        return $stmt->execute();
    }


    /**
     * Returns the users of a user-group in a class
     *
     * @param $classid
     * @param $groupid
     * @return array
     */
    function getUsers($classid, $groupid) {
        $stmt = $this->conn->prepare("select userid from usergroups where classid = :classid and groupid = :groupid");

        $stmt->bindParam(':classid', $classid);
        $stmt->bindParam(':groupid', $groupid);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Deletes a user from a class
     *
     * @param $classid
     * @param $userid
     * @return bool
     */
    function removeUserFromClass($classid, $userid) {
        $stmt = $this->conn->prepare("delete from usergroups where classid = :classid and userid = :userid");

        $stmt->bindParam(':classid', $classid);
        $stmt->bindParam(':userid', $userid);

        return $stmt->execute();
    }


    /**
     * Returns all admin userids
     *
     * @return array
     */
    function getAdmins(){
        $stmt = $this->conn->prepare("select distinct userid from usergroups where groupid = 4");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Returns the module code for a class
     *
     * @param $classId
     * @return array
     */
    function getModuleCodeForClassId($classId){
        $stmt = $this->conn->prepare("select modulecode from classes where classid = :classid");

        $stmt->bindParam(':classid', $classId);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    /**
     * Returns all of the locked worksheets for a class
     *
     * @param $classId
     * @return false|string
     */
    function getLockedWorksheetsArray($classId){
        $stmt = $this->conn->prepare("select * from worksheets where classid = :classid");
        $stmt->bindParam(':classid', $classId);
        $stmt->execute();

        $returnedData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $lockedWorksheets = [];

        for ($worksheetIndex = 0; $worksheetIndex < sizeof($returnedData); $worksheetIndex++) {
            if (($returnedData[$worksheetIndex]['lockdate'] != "") && ($returnedData[$worksheetIndex]['lockdate'] < date('Y-m-d'))){
                array_push($lockedWorksheets, $returnedData[$worksheetIndex]['worksheetno'] );
            }
        }

        return json_encode($lockedWorksheets) ;
    }


    /**
     * Returns the lock date of a worksheet
     *
     * @param $classId
     * @param $worksheet
     * @return mixed
     */
    function getLockdate($classId, $worksheet){
        $stmt = $this->conn->prepare("select lockdate from worksheets where classid = :classid and worksheetno = :worksheetno");
        $stmt->bindParam(':classid', $classId);
        $stmt->bindParam(':worksheetno', $worksheet);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN)[0];
    }
}