<?php
/**
 * Created by IntelliJ IDEA.
 * User: alext
 * Date: 26/03/2019
 * Time: 09:48
 */


class MemcachedDAO {
    private $conn = null;

    /*
     * Gets the connection details for memcached. Files are stored in the htdocs_private to increase security.
     */
    function __construct() {
        include_once '../../htdocs_private/data/MemcachedConnection.php';
        $this->conn = get_memcached_handle();
    }


    /**
     * Ensures connection details are reset
     */
    function closeConnection() {
        $conn = null;
    }


    /**
     * Submits a sign off request for a student
     *
     * @param $module
     * @param $time
     * @param $username
     * @param $computerid
     * @return string
     */
    function submitStudentSignOffRequest($module, $time, $username, $computerid) {
        $defaultDemonstratorAssignment = "nil";
        $separator = "&";

        $key_reference = $module . $separator . $time . $separator . $username;
        $request_content = $computerid . $separator . $defaultDemonstratorAssignment;

        // 2 hours (lesson length) is 7200 seconds, + 1 second due to memcached inequality comparison used
        $this->conn->set($key_reference, $request_content, 7201);
        return ($key_reference . "?" . $request_content);
    }


    /**
     * Retrieves the next sign-off request to be processed by a marker
     *
     * @param $moduleKey
     * @return bool|string
     */
    function getRequestToSignOff($moduleKey) {
        $matchedKeys = $this->getAllModulesKeys($moduleKey);
        $validData = $this->getValidDataFromKeys($matchedKeys);
        $sortedData = $this->sortKeysChronologically($validData);

        $request = $this->findNextFreeSignOffRequest($sortedData);

        return $request;
    }


    /**
     * Returns all keys from Memcached regardless of expiry but ensures they are of the relevant module
     *
     * @param $moduleKey
     * @return array
     */
    function getAllModulesKeys($moduleKey) {
        $allKeys = $this->conn->getAllKeys();
        $matchedKeys = [];

        foreach($allKeys as &$key) {
            $keyArray = explode("&", $key);

            if($keyArray[0] == $moduleKey) {
                array_push($matchedKeys, $key);
            }
        }

        return $matchedKeys;
    }


    /**
     * Fetches all keys given from memcached, expired keys will be rejected by memcached and are
     * not returned
     *
     * @param $matchedKeys
     * @return mixed
     */
    function getValidDataFromKeys($matchedKeys) {
        $validKeys = $this->conn->getMulti($matchedKeys);
        return $validKeys;
    }


    /**
     * Sorts the requests by which should be marked first
     *
     * @param $keys
     * @return mixed
     */
    function sortKeysChronologically($keys) {
        ksort($keys);
        return $keys;
    }


    /**
     * Gets the next sign-off request free from other markers
     *
     * @param $sortedDataList
     * @return bool|string
     */
    function findNextFreeSignOffRequest($sortedDataList) {
        foreach($sortedDataList as $key => $value) {
            if($this->checkRequestFreeOrAssignedToUser($value)) {

                return $key . "?" . $value;
            }
        }
        return false;
    }


    /**
     * Checks if the request can be selected by the current user
     *
     * @param $data
     * @return bool
     */
    function checkRequestFreeOrAssignedToUser($data) {
        $dataArray = explode("&", $data);
        if ($dataArray[1] == $_SESSION['username']){
            return true;
        } else if (($dataArray[1] == "nil")){
            return true;
        }
        return false;
    }


    /**
     * Returns the student name from a request
     *
     * @param $data
     * @return mixed
     */
    function getStudentNameFromRequest($data) {
        $keyandValueArray = explode("?", $data);
        $valueArray = explode("&", $keyandValueArray[0]);
        return $valueArray[2];
    }


    /**
     * Returns the location from a request
     *
     * @param $data
     * @return mixed
     */
    function getLocationFromRequest($data) {
        $keyandValueArray = explode("?", $data);
        $valueArray = explode("&", $keyandValueArray[1]);
        return $valueArray[0];
    }


    /**
     * Assigns a demonstrator to a request
     *
     * @param $data
     * @param $demonstratorUsername
     * @return bool
     */
    function setDemonstratorOnRequest($data, $demonstratorUsername) {
        $keyandValueArray = explode("?", $data);
        $valueArray = explode("&", $keyandValueArray[1]);

        $newRequestContent = $valueArray[0] . "&" . $demonstratorUsername;
        // after reassigning demonstrator, an expiration time of 2 hours is added once more
        return $this->conn->replace($keyandValueArray[0], $newRequestContent, 7201);
    }


    /**
     * Removes a sign-off request from memcached
     *
     * @param $signOffRequest
     * @return bool
     */
    function removeKey($signOffRequest){
        return $this->conn->delete($signOffRequest);
    }


    /**
     * Returns an existing sign-off request for a student
     *
     * @param $studentUsername
     * @param $moduleKey
     * @return string|null
     */
    function getExistingSignOff($studentUsername, $moduleKey){
        $matchedKeys = $this->getAllModulesKeys($moduleKey);
        $validData = $this->getValidDataFromKeys($matchedKeys);

        foreach($validData as $key => $content) {
            $keyComponents = explode("&", $key);
            if ($keyComponents[2] == $studentUsername){
                return $key . "?" . $content;
            }
        }
        return null;
    }


    /**
     * Obtains a request that is currently assigned to a marker
     *
     * @param $demonstratorsUsername
     * @param $moduleKey
     * @return string|null
     */
    function getDemonstratorsAssignedSignOff($demonstratorsUsername, $moduleKey){
        $matchedKeys = $this->getAllModulesKeys($moduleKey);
        $validData = $this->getValidDataFromKeys($matchedKeys);

        foreach($validData as $key => $content) {
            $keyComponents = explode("&", $content);
            if ($keyComponents[1] == $demonstratorsUsername){
                return $key . "?" . $content;
            }
        }
        return null;
    }


    /**
     * Returns the total number of free marking requests for a class.
     *
     * @param $moduleKey
     * @return int
     */
    function getNumberOfWaitingRequests($moduleKey){
        $matchedKeys = $this->getAllModulesKeys($moduleKey);
        $validData = $this->getValidDataFromKeys($matchedKeys);

        $numberWaiting = 0;
        foreach($validData as $key => $content) {
            $keyComponents = explode("&", $content);
            if ($keyComponents[1] == "nil"){
                $numberWaiting++;
            }
        }
        return $numberWaiting;
    }
}