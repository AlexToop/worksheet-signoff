<?php
session_start();
include 'User.php';

$username = strtolower($_POST["username"]);
$password = $_POST["password"];
$classId = $_POST["module"];

$user = new User($username, $password);
$user->checkLogin();
$user->checkUserGroup($classId);

if($user->getIsAuthenticated() && !empty($user->getUserGroup())) {
    setSessionDetails($user, $classId);
    redirectUser($user);

} else {
    resetDetails($user);
}


/**
 * Sets the session details for the user.
 *
 * @param $user
 * @param $classId
 */
function setSessionDetails($user, $classId){
    include_once 'DatabaseDAO.php';
    $dao = new DatabaseDAO();

    $_SESSION['username'] = $user->getUsername();
    $_SESSION['usergroup'] = $user->getUserGroup();
    $_SESSION['module'] = $dao->getModuleCodeForClassId($classId)[0];
    $_SESSION['classid'] = $classId;
}


/**
 * Sends the user to the correct portal.
 *
 * @param $user
 */
function redirectUser($user) {
    if($user->getUserGroup() == "student") {
        header('Location: ../students/home.php');

    } elseif($user->getUserGroup() == "demonstrator") {
        header("Location: ../demonstrators/home.php");

    } elseif($user->getUserGroup() == "lecturer") {
        header("Location: ../lecturers/home.php");

    } elseif($user->getUserGroup() == "admin") {
        header("Location: ../admin/home.php");

    } else {
        resetDetails($user);
    }
}


/**
 * Redirects the user back to the log in page and resets details.
 *
 * @param $user
 */
function resetDetails($user) {
    $redirect = "?username=" . $user->getUsername();
    $user = null;
    $_SESSION['username'] = null;
    $_SESSION['usergroup'] = null;
    $_SESSION['module'] = null;
    $_SESSION['status'] = "failedlogin";
    header(("Location: ../index.php?" . $redirect));
}