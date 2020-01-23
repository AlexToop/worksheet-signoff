<?php
session_start();

// enforces https use
if($_SERVER['HTTPS'] != "on") {
    $url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit;
}

// redirects users to the right home portal
if($context == "index" AND isset($_SESSION['username'])) {
    if($_SESSION['usergroup'] == "student") {
        header('Location: /students/home.php');
    }
    if($_SESSION['usergroup'] == "demonstrator") {
        header('Location: /demonstrators/home.php');
    }
    if($_SESSION['usergroup'] == "lecturer") {
        header('Location: /lecturers/home.php');
    }
    if($_SESSION['usergroup'] == "admin") {
        header('Location: /admin/home.php');
    }
}

// makes sure users go back to login if their PHP session variables are not set or have expired
if(($context != "index" AND $context != "about") AND
    (!isset($_SESSION['username']) OR
        !isset($_SESSION['usergroup']) OR
        !isset($_SESSION['module']))) {

    header('Location: ../index.php');
}


// redirects users if they try to enter a page without sufficient permissions
if($context == "student" AND $_SESSION['usergroup'] != "student") {
    header('Location: ../index.php');
}
if($context == "demonstrator" AND $_SESSION['usergroup'] == "student") {
    header('Location: ../index.php');
}
if(($context == "lecturer" AND $_SESSION['usergroup'] == "student") || ($context == "lecturer" AND $_SESSION['usergroup'] == "demonstrator")) {
    header('Location: ../index.php');
}
if(($context == "admin" AND $_SESSION['usergroup'] != "admin")) {
    header('Location: ../index.php');
}