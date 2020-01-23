<?php
session_start();
include_once '../scripts/MemcachedDAO.php';

// ensures data is reset as appropriate when logging out.

if (($_SESSION['usergroup'] == "demonstrator" || $_SESSION['usergroup'] == "lecturer" || $_SESSION['usergroup'] == "admin")){
    $memcached = new MemcachedDAO();
    $request = $memcached->getDemonstratorsAssignedSignOff($_SESSION['username'], $_SESSION["module"]);
    if(isset($request)) {
        $memcached->setDemonstratorOnRequest($request, "nil");
    }
}

if (($_SESSION['usergroup'] == "student")){
    $memcached = new MemcachedDAO();
    $request = $memcached->getExistingSignOff($_SESSION['username'], $_SESSION["module"]);
    if(isset($request)) {
        $keyAndValue = explode("?", $request);
        $memcached->removeKey($keyAndValue[0]);
    }
}

$redirectString = "?username=" . $_SESSION['username'];

session_destroy();
header('Location: ../index.php' . $redirectString);
?>
