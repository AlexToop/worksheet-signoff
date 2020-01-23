<?php

$context = "student";
include_once '../includes/Security.php';
include_once 'MemcachedDAO.php';

$memcached = new MemcachedDAO();

if(isset($_POST['removeQueueEntryKey'])) {
    removeQueueEntry($memcached, $_POST['queryToRemove']);

} elseif(isset($_POST['unassignQueueEntryKey'])) {
    unassignQueueEntry($memcached, $_POST['queryToUnassign']);

}

$memcached->closeConnection();


/////////////////////////// helper functions ////////////////////////////////////


/**
 * Removes a memcached request
 *
 * @param $memcached
 * @param $request
 */
function removeQueueEntry($memcached, $request) {
    $_SESSION['signOffStudent'] = null;

    $keyAndValue = explode("?", $request);

    echo ($memcached->removeKey($keyAndValue[0])) ? "done" : "fail";
}


/**
 * Un-assigns a marker from a request
 *
 * @param $memcached
 * @param $queueEntry
 */
function unassignQueueEntry($memcached, $queueEntry) {
    $_SESSION['signOffStudent'] = null;

    echo ($memcached->setDemonstratorOnRequest($queueEntry, "nil")) ? "done" : "fail";
}