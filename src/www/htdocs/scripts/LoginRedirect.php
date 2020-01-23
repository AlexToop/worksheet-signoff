<?php
include 'User.php';


$username = strtolower($_POST["username"]);
$password = $_POST["password"];
$user = new User($username, $password);
$user->checkLogin();

/**
 * Obtains the module details for an authenticated user as json
 */
if($user->getIsAuthenticated()) {
    include_once 'DatabaseDAO.php';
    $dao = new DatabaseDAO();

    $classes = $dao->getClassesRegisteredForUser($username);
    $returnArray = [];
    for($classIndex = 0; $classIndex < sizeof($classes); $classIndex++) {
        $classDetails = $dao->getClassesDetails($classes[$classIndex]);

        $classArray = [];
        array_push($classArray, $classDetails[0]['classid']);
        array_push($classArray, $classDetails[0]['modulecode']);
        array_push($classArray, $classDetails[0]['startyear']);

        array_push($returnArray, $classArray);
    }
    echo json_encode($returnArray);
} else {
    echo "failed";
}


?>