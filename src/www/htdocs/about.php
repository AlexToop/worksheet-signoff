<?php
$context = "about";
include_once 'includes/Security.php'
?>


<!DOCTYPE html>
<html lang="en">
<?php include_once 'includes/Header.php' ?>
<body>


<?php
$currentPage = "about";
include_once 'includes/Navigation.php'
?>


<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-xl-4  offset-xl-4 text-middle">

            <h1>About</h1>

            <h3>Overall</h3>
            <p>This system is intended for use at Aberystwyth University for helping manage classes utilising a
                worksheet sign-off system. Lecturers, demonstrators and students are all accommodated using this website
                solution. The website was created in 2019 by Alexander Toop.</p>

            <h3>FAQ</h3>
            <p><b>It says I'm not registered for any classes?</b> Ask your lecturer to check that you're registered in
                the class portal. <b>How do I start as a lecturer?</b> Please ask other lecturers with access to add
                your details.</p>

            <h3>Security</h3>
            <p>User credentials are sent using SSL to a University managed server to authenticate your credentials.
                Passwords are not stored and managed by this website.</p>

            <h3>Licences</h3>
            <p>Credit to 'Font Awesome' for all icons used in this website. The favicon icon was converted to a .ico format, other icons were not edited and all are
                licenced under <a target="_blank" href="https://fontawesome.com/license/free">CC BY 4.0 License</a>.</p>
            <p>Credit to the Bootstrap framework as used under the <a target="_blank" href="https://github.com/twbs/bootstrap/blob/master/LICENSE">MIT
                    license</a>.</p>
            <p>Credit to the jQuery Foundation for their library use in this project under the <a target="_blank" href="https://jquery.org/license/">MIT
                    license</a>.</p>

            <h3>Help</h3>
            <p class="pageEndPadding">Queries regarding this website can be directed to <a
                        href="mailto:alt28@aber.ac.uk?subject=Worksheet%20Sign-off%20System%20Query">alt28@aber.ac.uk</a>.
            </p>


        </div>
    </div>
</div>


</body>
</html>
