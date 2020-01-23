<?php
$context = "demonstrator";
include_once '../includes/Security.php';
$_SESSION['signOffStudent'] = null;
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/Header.php' ?>
<body>

<?php
$currentPage = "demonstratorHome";
include_once '../includes/Navigation.php';

if(isset($_SESSION['status']) && ($_SESSION['status'] == "noStudentsInQueue")) {
    echo "<script type='text/javascript'>alert('No students currently waiting in the queue.');</script>";
    $_SESSION['status'] = null;
}

// Provides checks for the memcached page content
include_once '../scripts/MemcachedDAO.php';
$memcached = new MemcachedDAO();
$request = $memcached->getDemonstratorsAssignedSignOff($_SESSION['username'], $_SESSION["module"]);
if(isset($request)) {
    $memcached->setDemonstratorOnRequest($request, "nil");
}

$studentsWaiting = $memcached->getNumberOfWaitingRequests($_SESSION["module"]);
$studentHtml = "<div id=\"StudentsWaitingInfo\" class=\"alert alert-info\" role=\"alert\">There are currently " . $studentsWaiting . " student(/s) waiting for sign-off.</div>";
?>

<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-xl-4  offset-xl-4 text-middle">
            <h1>Demonstrator - <?php echo strtoupper($_SESSION['module']) ?></h1>
            <p>Select the action you wish to take:</p>

            <?php echo $studentHtml ?>

            <a href="../shared/sign_off_portal.php">Enter student sign-off assignment portal</a>
            <p>This will allow you to view students that have requested to be signed off for a worksheet(/s).</p>

            <a href="../shared/search.php">Manually sign-off student</a>
            <p class="pageEndPadding">Allows you to search for a student username and change their worksheet sign-offs.</p>
        </div>
    </div>
</div>

</body>
</html>