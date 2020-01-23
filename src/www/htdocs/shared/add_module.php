<?php
$context = "lecturer";
include_once '../includes/Security.php';
?>


<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/Header.php' ?>
<body>


<?php
$currentPage = "lecturerAddModule";
include_once '../includes/Navigation.php'
?>


<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-xl-4  offset-xl-4 text-middle">
            <h1>Add a new module class</h1>

            <label for="newModule">Module code</label>
            <input required type="text" class="form-control" id="newModule" size="35" name="newModule"
                   placeholder="Example: CS10110">

            <label for="startyear">Academic year</label>
            <select id="addstartyear" name="years" class="form-control">
                <option value="selection1"><?php echo(date("Y") - 1) ?> / <?php echo date("Y") ?></option>
                <option value="selection2"><?php echo(date("Y")) ?> / <?php echo(date("Y") + 1) ?></option>
            </select>

            <label for="noOfWorksheets">Number of worksheets to create</label>
            <input required type="number" class="form-control" id="noOfWorksheets" size="35"
                   name="noOfWorksheets" placeholder="Example: 10">

            <label for="studentFileUpload">Students CSV file ('Last Name', 'First Name', 'Username' and 'Student ID' columns required).</label>
            </br>
            <input type="file" id="studentFileUpload" />

            <p></p>

            <button class="btn btn-primary" id="getWorksheetdetailsButton">Input worksheet details</button>

            <p></p>

            <div id="weightsHelpInfo" class="alert alert-info" role="alert">
                <b>--How to enter weights for each worksheet--</b><br/>
                <b>Case 1:</b> One question worth 5 marks, enter "<b>5</b>".<br/>
                <b>Case 2:</b> Three questions, worth 10, 20, 50 marks respectively, enter "<b>10, 20, 50</b>".<br/>
                <b>Case 3:</b> Two questions, question one has 3 parts, worth 10 marks each, the second question
                weighing 20 marks by itself, enter "<b>[10,10,10], [20]</b>".
            </div>

            <div id="addModuleDetails"></div>

            <div id="checkDetailsInfo" class="alert alert-info" role="alert">Please check the details entered before continuing. Some values cannot be changed without recreating the class.</div>

            <button class="btn btn-primary margin-top-20" id="submitNewClass">Create Class</button>

            <div class="pageEndPadding"></div>

        </div>
    </div>
</div>


</body>
</html>

<script type="text/javascript" src="../scripts/add.worksheets.js"></script>
