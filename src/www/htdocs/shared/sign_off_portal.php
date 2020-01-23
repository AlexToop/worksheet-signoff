<?php
// lecturers are permitted access to demonstrator content
$context = "demonstrator";
include_once '../includes/Security.php';

$module = strtoupper($_SESSION['module']);
$studentName = null;
$studentLocation = null;
$dismissDisabledStatus = null;

// if we've come direct from the search
if(!empty($_POST['studentToSignOff'])) {
    $dismissDisabledStatus = "disabled";
    $studentName = strtolower($_POST['studentToSignOff']);
    $_SESSION['signOffStudent'] = $studentName;
    $_SESSION['status'] = "fromSearch";
    $_SESSION['lastRequest'] = null;

    include_once '../scripts/DatabaseDAO.php';
    $dao = new DatabaseDAO();
    if($dao->isStudentRegisteredForClass($_SESSION['classid'], $studentName) != 1) {
        $_SESSION['status'] = "usernameNotFound";
        header('Location: search.php');
    }
    $dao->closeConnection();
} else if (($_SESSION['status'] == "fromSearch")) {
    $_SESSION['lastRequest'] = null;
    $studentName = $_SESSION['signOffStudent'];
}

//if we've got a student to edit saved in session by originally came from search
if(!empty($_SESSION['signOffStudent']) && ($_SESSION['status'] == "fromSearch")) {
    $dismissDisabledStatus = "disabled";
    $studentName = $_SESSION['signOffStudent'];
}


// if we're not from search and have no saved student --- get queue request
if((empty($_SESSION['signOffStudent']) && empty($_POST['studentToSignOff'])) || (($_SESSION['status'] == "fromQueue") && empty($_SESSION['signOffStudent']))) {
    $_SESSION['status'] = "fromQueue";
    include_once '../scripts/MemcachedDAO.php';
    $memcached = new MemcachedDAO();
    $request = $memcached->getRequestToSignOff($_SESSION['module']);

    if($request) {
        $studentName = $memcached->getStudentNameFromRequest($request);
        $studentLocation = $memcached->getLocationFromRequest($request);
        $memcached->setDemonstratorOnRequest($request, $_SESSION['username']);
        $_SESSION['signOffStudent'] = $studentName;
        $_SESSION['currStudentLocation'] = $studentLocation;
        $_SESSION['lastRequest'] = $request;
    } else {
        $_SESSION['status'] = "noStudentsInQueue";
        header('Location: ../index.php');
    }

    $memcached->closeConnection();
    $memcached = null;
} else if ($_SESSION['status'] == "fromQueue") {
    // from queue but still have a student assigned
    $studentName = $_SESSION['signOffStudent'];
    $studentLocation = $_SESSION['currStudentLocation'];
    $request = $_SESSION['lastRequest'];
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <?php include_once '../includes/Header.php' ?>
    <body>

    <?php
    $currentPage = "signOffs";
    include_once '../includes/Navigation.php';
    include_once '../includes/Modals.php';
    ?>

    <div class="container-fluid text-center">
        <div class="row content">
            <div class="col-xl-4  offset-xl-4 text-middle">
                <h1>Sign-off Portal - <?php echo $module ?></h1>

                <h3>Student: <?php echo $studentName; ?></h3>
                <h3>Location: <?php echo ($studentLocation) ? $studentLocation : "Unknown"; ?></h3>

                <div id="buttonGroups">
                    <div id="wks"></div>
                    <div id="q"></div>
                    <div id="p"></div>
                    <div id="percentageMarkDiv"></div>
                </div>

                <button <?php echo $dismissDisabledStatus; ?> type="button" class="btn btn-secondary margin_top60" data-toggle="modal"
                        data-target="#declineModel">
                    Dismiss
                </button>

                <button type="button" class="btn btn-primary margin_top60 margin_left30" id="submitChanges">
                    Add mark
                </button>

                <div class="pageEndPadding"></div>

            </div>
        </div>
    </div>

    </body>
    </html>


    <script>
        <?php
        // Provides class information for the javascript to use in display logic
        include_once '../scripts/DatabaseDAO.php';
        $dao = new DatabaseDAO();
        $lockedWorksheets = $dao->getLockedWorksheetsArray($_SESSION['classid']);
        echo "var classStructureArrays = " . $dao->getStructureOfClassId($_SESSION['classid']) . ";";
        echo "var doneSignOffs = " . $dao->getCompletedWorkForUserIdInClassId($_SESSION['classid'], $studentName) . ";";
        echo "var lockedWorksheets = " . $lockedWorksheets . ";";
        $dao->closeConnection();
        ?>

        var signOffsList = [];
        for (var currSignOff = 0; currSignOff < doneSignOffs.length; currSignOff++) {
            var itemToAdd = [doneSignOffs[currSignOff].worksheetno, doneSignOffs[currSignOff].questionno, doneSignOffs[currSignOff].questionpart, doneSignOffs[currSignOff].percentagedone];
            signOffsList.push(itemToAdd);
        }

    </script>
    <script type="text/javascript" src="../scripts/button.generation.logic.js"></script>
    <script type="text/javascript" src="../scripts/sign.off.portal.js"></script>