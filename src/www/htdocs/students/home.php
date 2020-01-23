<?php
$context = "student";
include_once '../includes/Security.php'
?>


<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/Header.php' ?>
<body>


<?php
$currentPage = "studentHome";
include_once '../includes/Navigation.php';
include_once '../scripts/MemcachedDAO.php';

// Checks to see if there is a current pending request.
$memcachedMessage = '';
$memcached = new MemcachedDAO();
$request = $memcached->getExistingSignOff($_SESSION['username'], $_SESSION["module"]);
if(isset($request)) {
    $memcachedMessage = "You have a demonstrator request pending for this module. <a href=\"#\" id='removeRequest'>Cancel Request</a>";
}

$hostname = null;
$inputValueToWrite = '';
$buttonValueToWrite = '';

// Checks for sources of the clients hostname. Credit to Alun Jones for his CGI assistance.
// Hrishikesh Mishra's code was used to obtain the hostname and did not require modification.
// Source: https://stackoverflow.com/questions/11452938/how-to-use-http-x-forwarded-for-properly

if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
    $hostname = gethostbyaddr($_SERVER['HTTP_X_FORWARDED_FOR']);
} elseif(array_key_exists('REMOTE_ADDR', $_SERVER)) {
    $hostname = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
} elseif(array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
    $hostname = gethostbyaddr($_SERVER["HTTP_CLIENT_IP"]);
}

// ensures the found hostname can be used to determine the computer id
if((isset($hostname)) && ($hostname !== "mmp-alt28.dcs.aber.ac.uk")) {
    $keyAndValueArray = explode(".", $hostname);
    $computerName = $keyAndValueArray[0];
    $computerNameKeyValueArray = explode("-", $computerName);
    if(sizeof($computerNameKeyValueArray) == 3) {
        $computerNo = $computerNameKeyValueArray[2];
        $inputValueToWrite .= 'value="' . $computerNo . '" ';
    }
}

if(isset($request)) {
    $inputValueToWrite .= 'disabled';
    $buttonValueToWrite = 'disabled';
}
?>


<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-xl-4  offset-xl-4 text-middle">

            <h1>Student - <?php echo strtoupper($_SESSION['module']) ?></h1>

            <p>When you've completed a question, enter your details below and submit to request
                a demonstrator to sign
                you off on the work.</p>

            <?php echo '<p>' . $memcachedMessage . '</p>'; ?>

            <form action="submit.php" method="post">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Computer ID</label>
                    <input required type="text" class="form-control" id="computerID" size="35" name="computerID"
                           placeholder="Example: A11" <?php echo $inputValueToWrite; ?>>
                </div>

                <button type="submit" <?php echo $buttonValueToWrite; ?> name="submit"
                        value="<?php echo date("H:i:s:u"); ?>" class="btn btn-primary">
                    Request sign-off
                </button>
            </form>
            <div class="pageEndPadding"></div>
        </div>
    </div>
</div>


<script>
    // Allows the student to remove their demonstrator request
    $(document).on("click", "#removeRequest", function (event) {
        var request = "<?php echo $request ?>";

        $.post('../scripts/MemcachedQueries.php', {
            queryToRemove: request,
            removeQueueEntryKey: 1
        }, function (data) {
            // See done.
        }).done(function (data) {
            if (data == "fail") {
                alert("Failed to remove request.");
            }
            location.reload();
        }).fail(function () {
            alert("Failed to remove request.");
        });
        return false;
    });
</script>


</body>
</html>
