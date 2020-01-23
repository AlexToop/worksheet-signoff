<?php
$context = "student";
include_once '../includes/Security.php'
?>


<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/Header.php' ?>
<body>


<?php
$currentPage = "studentSubmit";
include_once '../includes/Navigation.php';
include_once '../scripts/MemcachedDAO.php';
$memcached = new MemcachedDAO();
$requestSubmitted = $memcached->submitStudentSignOffRequest($_SESSION["module"], date("H:i:s"), $_SESSION['username'], $_POST["computerID"]);
?>


<!-- main content -->
<div class="container-fluid text-center">
    <div class="row content">

        <div class="col-xl-4  offset-xl-4 text-middle">

            <h1>Request sent - <?php echo strtoupper($_SESSION['module']) ?></h1>

            <p>Lecturers and demonstrators will now be able to see that you've requested to be
                signed-off.</p>

            <h3>Student: <?php echo $_SESSION['username'] ?></h3>

            <h3>Location: <?php echo $_POST["computerID"] ?></h3>


            <button id="removeDemonstratorRequest" class="btn btn-primary margin-top-20">
                Remove demonstrator request
            </button>

            <div class="pageEndPadding"></div>

        </div>
    </div>
</div>


</body>
</html>


<script>
    // allows the student to remove their demonstrator request
    $(document).on("click", "#removeDemonstratorRequest", function (event) {

        $.post('../scripts/MemcachedQueries.php', {
            queryToRemove: "<?php echo $requestSubmitted ?>",
            removeQueueEntryKey: 1
        }, function (data) {
            // See done.
        }).done(function (data) {
            if (data == "fail") {
                alert("Failed to remove request.");
            }
            location.href = 'home.php';
        }).fail(function () {
            alert("Failed to remove request.");
        });
        return false;
    });
</script>