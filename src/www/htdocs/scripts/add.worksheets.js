$('#weightsHelpInfo').hide();
$('#submitNewClass').hide();
$('#checkDetailsInfo').hide();
var isWorksheetSectionMade = false;
var lastAcceptedWorksheetNo = null;
var lastAcceptedStartYear = null;
var lastAcceptedModuleName = null;
var acceptedFileInput = [];
var isFileAdded = false;
document.getElementById('studentFileUpload').addEventListener('change', readStudentFileData);

/**
 * Generates input areas to get more information about worksheet structure
 * if the correct data has currently been entered.
 */
$('#getWorksheetdetailsButton').on('click', function () {
        var moduleName = $('#newModule').val();
        var noWorksheets = ($('#noOfWorksheets').val() == "") ? 0 : parseInt($('#noOfWorksheets').val());
        var moduleYear = $('#addstartyear').children("option:selected").val();

        if (moduleName == "") {
            $('#newModule').val(lastAcceptedModuleName);
            alert("Please enter a module name.");

        } else if ((noWorksheets > 50) || (noWorksheets < 1)) {
            $('#noOfWorksheets').val(lastAcceptedWorksheetNo);
            alert("Please enter a number of worksheets between 1 and 50.");

        } else if (!isFileAdded) {
            alert("Please upload a student CSV file.");

        } else if (isWorksheetSectionMade) {
            document.getElementById("addModuleDetails").innerHTML = "";
            lastAcceptedWorksheetNo = noWorksheets;
            lastAcceptedStartYear = moduleYear;
            lastAcceptedModuleName = moduleName.toLowerCase();
            displayWorksheetData($('#noOfWorksheets').val());

        } else {
            isWorksheetSectionMade = true;
            lastAcceptedWorksheetNo = noWorksheets;
            lastAcceptedStartYear = moduleYear;
            lastAcceptedModuleName = moduleName.toLowerCase();
            $('#weightsHelpInfo').show();
            $('#checkDetailsInfo').show();
            $('#submitNewClass').show();
            $('#getWorksheetdetailsButton').text("Update");
            $('#getWorksheetdetailsButton').removeClass("btn-primary");
            $('#getWorksheetdetailsButton').addClass("btn-warning");
            displayWorksheetData($('#noOfWorksheets').val());
        }
    }
);


/**
 * The final submit button that checks the data, before starting a function to send the data off.
 */
$('#submitNewClass').on('click', function () {
        if (areWeightTextFieldsComplete(lastAcceptedWorksheetNo)) {
            if (areWorksheetsAreValid()) {
                sendOffNewClass();
            } else {
                alert("Please check the values entered into the weight fields for worksheets. The three cases provided near the top of the page should help you format the worksheet weights.");
            }
        } else {
            alert("Please complete entering the details into the weight fields.");
        }
    }
);


/**
 * Presents input fields to allow users to enter worksheet information.
 *
 * @param noWorksheetsToCreate
 */
function displayWorksheetData(noWorksheetsToCreate) {
    var worksheetHtml = "<p>Date to lock edits (if needed) -- Weightings</p>";
    worksheetHtml += getInputFieldsForWorksheets(noWorksheetsToCreate);
    document.getElementById('addModuleDetails').insertAdjacentHTML('beforeend', worksheetHtml);
}


/**
 * Generates and returns HTML for a number of worksheet input fields.
 *
 * @param noWorksheets
 * @returns {string}
 */
function getInputFieldsForWorksheets(noWorksheets) {
    var output = "";
    for (var worksheetNo = 1; worksheetNo <= noWorksheets; worksheetNo++) {
        var labelText = "Worksheet " + worksheetNo + "-";
        var idAndNameForLockDate = "worksheet" + worksheetNo + "lockdate";
        var idAndNameForWeights = "worksheet" + worksheetNo + "weights";

        output += "<label>" + labelText + "</label>";
        output += "<input type=\"date\" class=\"form-control wksEntryLarge\" id=\"" + idAndNameForLockDate + "\" size=\"15\" name=\"" + idAndNameForLockDate + "\"\n" +
            "placeholder=\"dd/mm/yyyy\"> <input type=\"text\" class=\"form-control wksEntryXLarge\" id=\"" + idAndNameForWeights + "\" size=\"15\" name=\"" + idAndNameForWeights + "\"\n" +
            "placeholder=\"10, 10\"></br></br>";
    }
    return output;
}


/**
 * Checks to see if input fields have been filled in.
 *
 * @param noWeights
 * @returns {boolean}
 */
function areWeightTextFieldsComplete(noWeights) {
    for (var worksheetNo = 1; worksheetNo <= noWeights; worksheetNo++) {
        var worksheetWeightId = "#worksheet" + worksheetNo + "weights";
        if ($(worksheetWeightId).val() == "") {
            return false;
        }
    }
    return true;
}


/**
 * Breaks down an input string to provide an easy to process string containing mark weights.
 *
 * @param weightsText
 * @param delimiterForQuestions
 * @returns {string}
 */
function getWorksheetWeightsToSubmit(weightsText, delimiterForQuestions) {
    var testWeights = JSON.parse("[" + weightsText + "]");
    var finalOutput = "";

    for (var questionWeightIndex = 0; questionWeightIndex < testWeights.length; questionWeightIndex++) {
        if (testWeights[questionWeightIndex] instanceof Array) {
            for (var partWeightIndex = 0; partWeightIndex < testWeights[questionWeightIndex].length; partWeightIndex++) {
                finalOutput += testWeights[questionWeightIndex][partWeightIndex];
                if ((partWeightIndex + 1) < testWeights[questionWeightIndex].length) {
                    finalOutput += ",";
                }
            }
        } else {
            finalOutput += testWeights[questionWeightIndex];
        }
        if ((questionWeightIndex + 1) < testWeights.length) {
            finalOutput += delimiterForQuestions;
        }
    }
    finalOutput = finalOutput.replace(/ /g, '');
    return finalOutput;
}


/**
 * Removes symbols used in the text input for worksheet structure.
 *
 * @param weightsText
 * @param delimiterForQuestions
 * @returns {*}
 */
function getParsableWorksheetWeights(weightsText, delimiterForQuestions) {
    weightsText = weightsText.replace(/ /g, '');
    weightsText = weightsText.replace(/\[/g, "");
    weightsText = weightsText.replace(/\],/g, delimiterForQuestions);
    weightsText = weightsText.replace(/\]/g, "");
    return weightsText;
}


/**
 * Determines that worksheet inputs on the page all contain parsable weights.
 *
 * @returns {boolean}
 */
function areWorksheetsAreValid() {
    var noOfWorksheets = $('#noOfWorksheets').val();
    for (var worksheetNo = 1; worksheetNo <= noOfWorksheets; worksheetNo++) {

        var worksheetWeightId = "#worksheet" + worksheetNo + "weights";
        var worksheetWeight = $(worksheetWeightId).val();
        worksheetWeight = getParsableWorksheetWeights(worksheetWeight, ",");
        worksheetWeight = worksheetWeight.split(",");

        for (var entryNo = 0; entryNo < worksheetWeight.length; entryNo++) {
            if (worksheetWeight[entryNo].match(/^[0-9]+$/g) == null) {
                return false;
            }
        }
    }
    return true;
}


/**
 * Returns an array of weights, with the internal arrays representing sub-weights.
 *
 * @param weight
 * @returns {*}
 */
function getWeightArrayForParsableWeightText(weight) {
    weight = weight.split("?");

    for (var questionNo = 0; questionNo < weight.length; questionNo++) {
        weight[questionNo] = weight[questionNo].split(",");
        for (var partNo = 0; partNo < weight[questionNo].length; partNo++) {
            weight[questionNo][partNo] = parseInt(weight[questionNo][partNo]);
        }
    }
    return weight;
}


/**
 * Sends off the class information from the page.
 *
 * @returns {boolean}
 */
function sendOffNewClass() {
    var arrayToSend = [];
    arrayToSend.push(lastAcceptedModuleName);

    for (var worksheetNo = 1; worksheetNo <= lastAcceptedWorksheetNo; worksheetNo++) {
        var worksheetLockDateId = "#worksheet" + worksheetNo + "lockdate";
        var worksheetWeightId = "#worksheet" + worksheetNo + "weights";
        var worksheetLockdate = $(worksheetLockDateId).val();

        var worksheetWeight = $(worksheetWeightId).val();
        worksheetWeight = getWorksheetWeightsToSubmit(worksheetWeight, "?");
        worksheetWeight = getWeightArrayForParsableWeightText(worksheetWeight);

        arrayToSend.push([worksheetLockdate, worksheetWeight]);
    }

    $.post('../scripts/DatabaseQueries.php', {
        addModule: arrayToSend,
        students: acceptedFileInput,
        startyear: lastAcceptedStartYear,
        createNewModuleKey: 1
    }, function (data) {

        if (data == "fail") {
            alert("Issue creating the new class. Please check your module code and academic year is not already created. Or that the student CSV file provided is as expected.");
        } else {
            alert("The class has been successfully created. Please check the classes details.");
            location.href = '../shared/view_modules.php';
        }

    }).fail(function () {
        alert("Issue creating the new module. Please check your module code and academic year is not already created. Or that the student CSV file provided is as expected.");
    });
    //Prevent page refresh
    return false;
}


/**
 * Converts the contents of the file uploaded into an array that can be read.
 * The method created is based several contributions to
 * https://stackoverflow.com/questions/27254735/filereader-onload-with-result-and-parameter
 * and was modified for readability.
 *
 * @param input
 */
function readStudentFileData(input) {
    var file = input.target.files[0];

    if (file) {
        var fileReader = new FileReader();
        isFileAdded = true;

        fileReader.onload = function (event) {
            var contents = event.target.result;
            var output = [];
            var lines = contents.split("\n");

            for (var currentLine = 0; currentLine < lines.length; currentLine++) {
                var lineContentsArray = lines[currentLine].split(",");
                output[currentLine] = lineContentsArray;
            }
            acceptedFileInput = output;
        };

        fileReader.readAsText(file);
    } else {
        alert("Failed to load file");
    }
}