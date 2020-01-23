<?php
// lecturers are permitted access to demonstrator content
$context = "demonstrator";
include_once '../includes/Security.php';
$_SESSION['signOffStudent'] = null;
?>


<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/Header.php' ?>
<body>


<?php
$currentPage = "studentSearch";
include_once '../includes/Navigation.php'
?>


<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-xl-4  offset-xl-4 text-middle">
            <h1>Sign-off search - <?php echo strtoupper($_SESSION['module']) ?></h1>
            <p>Please enter the username of the student you wish to edit the marks of:</p>

            <form action="sign_off_portal.php" method="post">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Student username:</label>
                    <input required type="text" class="form-control" id="studentToSignOff" size="35"
                           name="studentToSignOff"
                           placeholder="Example: alt28">
                </div>

                <?php
                // Provides feedback if the user could not be found after entering the sign_off_portal
                if(isset($_SESSION['status']) && ($_SESSION['status'] == "usernameNotFound")) {
                    echo "<script type='text/javascript'>alert('Student username is not registered for this module.');</script>";
                    $_SESSION['signOffStudent'] = null;
                    $_SESSION['status'] = null;
                }
                ?>

                <button type="submit" name="submit" class="btn btn-primary">
                    Find student
                </button>
            </form>
            <div class="pageEndPadding"></div>
        </div>
    </div>
</div>


</body>
</html>
