<?php
$context = "lecturer";
include_once '../includes/Security.php';
include_once '../includes/Modals.php';
include_once '../scripts/DatabaseDAO.php';
$dao = new DatabaseDAO();


// gets the classes the user is a part of
$classListRegisteredFor = $dao->getClassesRegisteredForUser($_SESSION['username']);
$arrayClassDetailsRegisteredFor = [];

// gets the details for each class
foreach($classListRegisteredFor as $key => $classid) {
    $classDetails = $dao->getClassesDetails($classid)[0];
    $classDetails['classid'] = $classid;
    $classDetails['students'] = $dao->getUsersInModule($classid, 1);
    $classDetails['demonstrators'] = $dao->getUsersInModule($classid, 2);
    $classDetails['lecturers'] = $dao->getUsersInModule($classid, 3);
    array_push($arrayClassDetailsRegisteredFor, $classDetails);
}

// displays each class and provides interaction buttons
$outputClassesHTML = "";
foreach($arrayClassDetailsRegisteredFor as &$classInfo) {
    if($classInfo['modulecode'] !== "class-placeholder" || $_SESSION['usergroup'] == "admin") {
        $outputClassesHTML .= "<h4>Module: " . strtoupper($classInfo['modulecode']) . ". Academic year: " . $classInfo['startyear'] . " / " . ($classInfo['startyear'] + 1) . ".</h4>";
        $outputClassesHTML .= "<p>Students: " . sizeof($classInfo['students']) . ", demonstrators: " . sizeof($classInfo['demonstrators']) . " and lecturers: " . sizeof($classInfo['lecturers']) . ".</p>";
        $outputClassesHTML .= "<div class=\"btn-group flex-wrap\" role=\"group\" aria-label=\"Basic example\">";
        $outputClassesHTML .= "<button class=\"btn btn-primary border-secondary\" onclick=\"getArrayOfModuleData(" . $classInfo['classid'] . ")\">Export Class</button>";
        $outputClassesHTML .= "<button class=\"btn btn-primary border-secondary\" onclick=\"getPercentagesLockData(" . $classInfo['classid'] . ")\">Export Lock-dates/Percentages</button>";
        $outputClassesHTML .= "<button class=\"btn btn-primary border-secondary\" onclick=\"amendGcUpload(" . $classInfo['classid'] . ")\">Amend GC CSV</button>";
        $outputClassesHTML .= "<button class=\"btn btn-warning border-secondary\" onclick=\"editUsers(" . $classInfo['classid'] . ", 1)\">Edit Students</button>";
        $outputClassesHTML .= "<button class=\"btn btn-warning border-secondary\" onclick=\"editUsers(" . $classInfo['classid'] . ", 2)\">Edit Demonstrators</button>";
        $outputClassesHTML .= "<button class=\"btn btn-warning border-secondary\" onclick=\"editUsers(" . $classInfo['classid'] . ", 3)\">Edit Lecturers</button>";
        $outputClassesHTML .= "<button class=\"btn btn-danger border-secondary\" onclick=\"deleteModuleDialog(" . $classInfo['classid'] . ", '" . strtoupper($classInfo['modulecode']) . "')\">Delete</button>";
        $outputClassesHTML .= "</div>";
        $outputClassesHTML .= "<hr>";
    }
}

$dao->closeConnection();
?>


<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/Header.php' ?>
<body>


<?php
$currentPage = "lecturerViewModules";
include_once '../includes/Navigation.php'
?>


<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-xl-4  offset-xl-4 text-middle">

            <h1>View your modules</h1>

            <div class="alert alert-info" role="alert">Please check the class details are correct before recording student marks. Some class structure details can be changed only by remaking the class.</div>

            <?php echo $outputClassesHTML; ?>

            <div class="pageEndPadding"></div>
        </div>
    </div>
</div>


<script type="text/javascript" src="../scripts/class.editing.js"></script>


</body>
</html>
