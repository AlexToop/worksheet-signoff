<?php
$logoutLiElement = '';

if(($currentPage !== 'index' AND $currentPage !== 'about') OR ($currentPage == 'about' AND isset($_SESSION['username']))) {
    $logoutLiElement = '<a href="#logoutModel" data-toggle="modal" data-target="#logoutModel"><span class="fa fa-sign-out	"></span> Logout / change module (' . $_SESSION['username'] . ')</a>';
}
if($currentPage == 'index' OR ($currentPage == 'about' AND !isset($_SESSION['username']))) {
    $logoutLiElement = '<a href="index.php"><span class="fa fa-sign-in"></span> Login</a>';
}
?>


<nav class="navbar navbar-expand-lg navbar-light nav-aber-yellow">
    <div class="container">

        <a class="navbar-brand" href="../index.php">Online signing-off</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#signOffNavbar"
                aria-controls="signOffNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="signOffNavbar">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="../index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../about.php">About</a>
                </li>
            </ul>
            <?php echo $logoutLiElement ?>
        </div>

    </div>
</nav>


<!-- breadcrumbs depend on the context -->
<div class="breadcrumbs">
    <ul class="breadcrumb d-flex justify-content-center">
        <?php
        if($currentPage == 'about') {
            echo '<li class="breadcrumb-item active">About</a></li>';
        } elseif($currentPage == 'index' OR $currentPage == 'studentHome' OR $currentPage == 'demonstratorHome' OR $currentPage == 'lecturerHome' OR $currentPage == 'adminHome') {
            echo '<li class="breadcrumb-item active">Home</a></li>';
        } elseif($currentPage == 'studentSubmit') {
            echo '<li class="breadcrumb-item"><a href="../students/home.php">Home</a></li><li class="breadcrumb-item active">Request</a></li>';
        } elseif($currentPage == 'lecturerViewModules' AND ($_SESSION['usergroup'] == "lecturer" || $_SESSION['usergroup'] == "admin")) {
            echo '<li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item active">View Modules</a></li>';
        } elseif($currentPage == 'signOffs' AND ($_SESSION['usergroup'] == "lecturer" || $_SESSION['usergroup'] == "admin" || $_SESSION['usergroup'] == "demonstrator")) {
            echo '<li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item active">Sign-offs</a></li>';
        } elseif($currentPage == 'studentSearch' AND $_SESSION['usergroup'] == "demonstrator") {
            echo '<li class="breadcrumb-item"><a href="../demonstrators/home.php">Home</a></li><li class="breadcrumb-item active">Search</a></li>';
        } elseif($currentPage == 'studentSearch' AND ($_SESSION['usergroup'] == "lecturer" || $_SESSION['usergroup'] == "admin")) {
            echo '<li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item active">Search</a></li>';
        } elseif($currentPage == 'lecturerAddModule' AND ($_SESSION['usergroup'] == "lecturer" || $_SESSION['usergroup'] == "admin")) {
            echo '<li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item active">Add Module</a></li>';
        }
        ?>
    </ul>
</div>


<!-- always needed no matter the page -->
<div class="modal" id="logoutModel" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log out / change module</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you wish to log out? Logging out will allow you to change module.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"
                        onclick="window.location.href = '../../scripts/LogoutRedirect.php'">Log out
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>