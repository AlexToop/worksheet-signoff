<?php
$context = "index";
include_once 'includes/Security.php'
?>


<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/Header.php' ?>
<body>


<?php
$currentPage = "index";
include_once 'includes/Navigation.php';
?>


<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-xl-4  offset-xl-4 text-middle">

            <h1>Welcome, please log in to continue.</h1>
            <p>Username and password are authenticated through Aberystwyth University.</p>


            <label class="largeTopPadding" for="exampleFormControlInput1">University username</label>
            <input required type="text" class="form-control" id="username" size="35" name="username"
                   placeholder="Example: aaa01"
                <?php
                $username = (isset($_GET['username'])) ? $_GET['username'] : null;
                echo 'value="' . $username . '"';
                ?>>

            <label for="inputPassword">Password</label>
            <input required type="password" class="form-control" id="password" size="35" name="password"
                   placeholder="Password">

            <div class="margin-top-20" id="failedMessageArea"></div>

            <p id="passwordReset" class="smallTopPadding smallBtmPadding">If you've forgotten your password, please
                reset it with the
                following link: <a href="https://myaccount.aber.ac.uk/open/reset/" target="_blank">https://myaccount.aber.ac.uk/open/reset/</a>
            </p>

            <button id="initLoginButton" class="btn btn-primary">Log in</button>

            <div id="showModules"></div>

            <button id="nextButton" class="btn btn-primary">Next</button>

            <div class="pageEndPadding"></div>

        </div>
    </div>
</div>


</body>
</html>

<script type="text/javascript" src="scripts/login.js"></script>
