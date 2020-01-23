<?php
$context = "lecturer";
include_once '../includes/Security.php';
$_SESSION['signOffStudent'] = null;
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/Header.php' ?>
<body>

<?php
$currentPage = "lecturerHome";
include_once '../includes/Navigation.php';
include_once '../includes/Modals.php';

if(isset($_SESSION['status']) && ($_SESSION['status'] == "noStudentsInQueue")) {
    echo "<script type='text/javascript'>alert('No students currently waiting in the queue.');</script>";
    $_SESSION['status'] = null;
}

// Provides checks for the memcached content.
include_once '../scripts/MemcachedDAO.php';
$memcached = new MemcachedDAO();
$request = $memcached->getDemonstratorsAssignedSignOff($_SESSION['username'], $_SESSION["module"]);
if(isset($request)) {
    $memcached->setDemonstratorOnRequest($request, "nil");
}

$studentsWaiting = $memcached->getNumberOfWaitingRequests($_SESSION["module"]);
$studentHtml = "<div id=\"StudentsWaitingInfo\" class=\"alert alert-info\" role=\"alert\">There are currently " . $studentsWaiting . " student(/s) waiting for sign-off.</div>";

// Provides small information box if in the placeholder module currently.
if($_SESSION['module'] == "class-placeholder") {
    $studentHtml = "<div id=\"StudentsWaitingInfo\" class=\"alert alert-info\" role=\"alert\">You are logged into a placeholder module. Create a new module and then log into that module to access all features.</div>";
}
?>

<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-xl-4  offset-xl-4 text-middle">
            <h1>Lecturer - <?php echo strtoupper($_SESSION['module']) ?></h1>
            <p>Select the action you wish to take:</p>

            <?php echo $studentHtml ?>

            <a id="queuePortalLink" href="../shared/sign_off_portal.php">Enter student sign-off assignment portal</a>
            <p>This will allow you to view students that have requested to be signed off for a worksheet(/s).</p>

            <a id="queuePortalLink" href="../shared/search.php">Manually sign-off student</a>
            <p>Allows you to search for a student username and change their worksheet sign-offs.</p>

            <a href="../shared/view_modules.php">View module classes</a>
            <p>View the modules available and review results.</p>

            <a href="../shared/add_module.php">Add module class</a>
            <p>Upload student list and provide details about the classes marking structure.</p>

            <a href="#" id="editTempLecturers">Add new lecturer</a>
            <p class="pageEndPadding">Add a new lecturer to a temporary class, allowing them to create a new class.</p>

        </div>
    </div>
</div>

</body>
</html>

<script type="text/javascript" src="../scripts/class.editing.js"></script>
<script>
    // Provides on click event to open the edit menu.
    $(document).on("click", "#editTempLecturers", function (event) {
        createEditMenu(3, "", 1);
    });
</script>