// Variables helped to maintain logic to determine what has been chosen.
var isWorksheetSelected = false;
var isQuestionSelected = false;
var isQuestionPartSelected = false;
var worksheetSelected = null;
var questionSelected = null;
var questionPartSelected = null;
var signOffChange = "";

// start of main page options creation
var noWorksheets = classStructureArrays.length;
var buttonGroupHtml = getButtonGroupHtml(noWorksheets, "wks");
writeButtonGroup("wks", buttonGroupHtml);
writeClickEvents(noWorksheets, "wks");

/**
 * Creates a button group on the page as relevant to the database data given
 *
 * @param noButtons The number of buttons in the group
 * @param idType "wks"/"q"/"p". Determines label and following logic to create the buttons
 */
function writeButtonGroup(idType, buttonGroupHtml) {
    var labelText;

    if (idType == "wks") {
        labelText = "Worksheet number:";
    } else if (idType == "q") {
        labelText = "Question:";
    } else if (idType == "p") {
        labelText = "Part:";
    } else {
        labelText = "'" + idType + "':";
    }

    if (idType == "q" && buttonGroupHtml == "") {
        isQuestionSelected = true;
        questionSelected = "q1";
        isQuestionPartSelected = true;
        questionPartSelected = "p1";
        showPercentageMarkSelection();
        createSubmissionOnClick();
    } else if (idType == "p" && buttonGroupHtml == "") {
        isQuestionPartSelected = true;
        questionPartSelected = "p1";
        showPercentageMarkSelection();
        createSubmissionOnClick();
    } else {
        var labelElement = document.createElement("label");
        var textNode = document.createTextNode(labelText);
        var queuePortalIdForType = document.getElementById(idType);
        labelElement.appendChild(textNode);
        queuePortalIdForType.appendChild(labelElement);
        queuePortalIdForType.insertAdjacentHTML('beforeend', buttonGroupHtml);
    }
}


/**
 * Returns html for buttons to be created for the given id type.
 *
 * @param numberToCreate How many buttons in the created html group.
 * @param buttonType Type should be "wks", "q", or "p".
 * @returns {string} The html output
 */
function getButtonGroupHtml(numberToCreate, buttonType) {
    var worksheetNoToSend = "";
    if (worksheetSelected) {
        worksheetNoToSend = worksheetSelected.replace("wks", "");
    }

    if ((numberToCreate == 1) && (buttonType == "q") && (getNoButtonsToCreate("q1", "q", worksheetNoToSend) == 1)) {
        return "";
    } else if ((numberToCreate == 1) && (buttonType == "p")) {
        return "";
    }

    var output = "<div class=\"btn-group flex-wrap\">";
    for (var buttonNo = 1; buttonNo < (numberToCreate + 1); buttonNo++) {

        var isMaxDepth = false;
        if (buttonType == "wks"){
            if (getNoButtonsToCreate((buttonType + buttonNo), buttonType, buttonNo) == 1){
                isMaxDepth = (getNoButtonsToCreate(("q" + 1), "q", buttonNo) == 1) ? true : false;
            }
        } else {
            isMaxDepth = (getNoButtonsToCreate((buttonType + buttonNo), buttonType, worksheetNoToSend) == 1) ? true : false;
        }

        output += getButtonHtml(buttonType, buttonNo, classStructureArrays, signOffsList, worksheetSelected, questionSelected, lockedWorksheets, isMaxDepth);
    }
    output += "</div>";

    return output;
}


/**
 * Creates all the click events for the buttons created on a page.
 *
 * @param noOfButtons
 * @param buttonType
 */
function writeClickEvents(noOfButtons, buttonType) {
    var nextButtonType = null;
    if (buttonType == "wks") {
        nextButtonType = "q";
    } else {
        nextButtonType = (buttonType == "q") ? "p" : "done";
    }

    for (var buttonNo = 1; buttonNo <= noOfButtons; buttonNo++) {

        var buttonId = '#' + buttonType + buttonNo;
        $(buttonId).on('click', function () {
                doOnClick(buttonType, nextButtonType, $(this).attr('id'));
            }
        );
    }
}


/**
 * Logic determining what happens in click events.
 *
 * @param type
 * @param nextTypeToCheck
 * @param id
 */
function doOnClick(type, nextTypeToCheck, id) {
    if (type == "wks") {
        worksheetOnClick(id);
    } else if (type == "q") {
        questionOnClick(id);
    } else if (nextTypeToCheck == "done") {
        questionPartOnClick(id);
    }
}


/**
 * Determines the logic to follow when a button is clicked
 *
 * @param id
 */
function questionPartOnClick(id) {
    if (isQuestionPartSelected) {
        $("#" + questionPartSelected).removeClass("active");
        $("#" + id).addClass("active");
        resetToPart();
        questionPartSelected = id;
        isQuestionPartSelected = true;
    } else {
        questionPartSelected = id;
        isQuestionPartSelected = true;
        $("#" + id).addClass("active");
        showPercentageMarkSelection();
        createSubmissionOnClick();
    }
}


/**
 * Performs logic when a worksheet is clicked. Either resets the page to a prior status or
 * creates the next section of interactions.
 *
 * @param id
 */
function worksheetOnClick(id) {
    if (isWorksheetSelected) {
        $("#" + worksheetSelected).removeClass("active");
        $("#" + id).addClass("active");

        resetToWorksheet(id);

        var noPartsToMake = getNoButtonsToCreate(id, "wks", worksheetSelected.replace("wks", ""));
        var buttonGroupHtml = getButtonGroupHtml(noPartsToMake, "q");
        writeButtonGroup("q", buttonGroupHtml);
        writeClickEvents(noPartsToMake, "q");
    } else {

        worksheetSelected = id;
        isWorksheetSelected = true;
        $("#" + id).addClass("active");
        var noQuestionsToMake = getNoButtonsToCreate(id, "wks", worksheetSelected.replace("wks", ""));
        var buttonGroupHtml = getButtonGroupHtml(noQuestionsToMake, "q");
        writeButtonGroup("q", buttonGroupHtml);
        writeClickEvents(noQuestionsToMake, "q");
    }
}


/**
 * Performs logic when a question is clicked. Either resets the page to a prior status or
 * creates the next section of interactions.
 *
 * @param id
 */
function questionOnClick(id) {
    var worksheetNoToSend = "";
    if (worksheetSelected) {
        worksheetNoToSend = worksheetSelected.replace("wks", "");
    }
    if (isQuestionSelected) {
        $("#" + questionSelected).removeClass("active");
        $("#" + id).addClass("active");
        resetToQuestion(id);
        var noPartsToMake = getNoButtonsToCreate(id, "q", worksheetNoToSend);
        var buttonGroupHtml = getButtonGroupHtml(noPartsToMake, "p");
        writeButtonGroup("p", buttonGroupHtml);
        writeClickEvents(noPartsToMake, "p");
    } else {
        questionSelected = id;
        isQuestionSelected = true;
        $("#" + id).addClass("active");
        var noPartsToMake = getNoButtonsToCreate(id, "q", worksheetNoToSend);
        var buttonGroupHtml = getButtonGroupHtml(noPartsToMake, "p");
        writeButtonGroup("p", buttonGroupHtml);
        writeClickEvents(noPartsToMake, "p");
    }
}


/**
 * Creates an on click event for the submit changes button. On click a confirmation modal is shown
 * where the marks can be sent off.
 */
function createSubmissionOnClick() {
    $('#submitChanges').on('click', function () {

        signOffChange = worksheetSelected + "!" + questionSelected + "!" + questionPartSelected + "!" + getMarkGiven();
        var formattedSignOffChange = "Worksheet: " + worksheetSelected.replace("wks", "") + " (question: " +
            questionSelected.replace("q", "") + ", part: " + questionPartSelected.replace("p", "") + "). Mark = " + getMarkGiven() + "%.";

        resetButtons();

        document.getElementById("confirmationText").innerHTML = "<p>Please confirm you would like to submit the following changes to the students worksheet sign-offs:</p><p>(Please ignore questions and parts if not relevant and showing 1.)</p>";
        var para = document.createElement("confirmationDetails");
        para.appendChild(document.createTextNode(formattedSignOffChange));
        document.getElementById("confirmationText").appendChild(para);

        $('#submitRequestToDatabase').on('click', function () {
            sendWorksheetSignOff();
        });

        $('#submitModel').modal('show');
    });
}


/**
 * Determines how many buttons to make for the current selection
 *
 * @param id
 * @param type
 * @returns {number}
 */
function getNoButtonsToCreate(id, type, worksheetNo) {
    if (type == "wks") {
        var strippedNo = id.replace("wks", "");
        var positionOfQuestionsArrayToCheck = parseInt(strippedNo) - 1;
        return classStructureArrays[positionOfQuestionsArrayToCheck].length;
    }
    if (type == "q") {
        var questionNo = id.replace("q", "");
        var worksheetPosition = parseInt(worksheetNo) - 1;
        var questionPosition = parseInt(questionNo) - 1;
        return classStructureArrays[worksheetPosition][questionPosition];
    }
    return 0;
}


/**
 * Resets all buttons and maintained variables
 */
function resetButtons() {
    isWorksheetSelected = false;
    isQuestionSelected = false;
    isQuestionPartSelected = false;
    worksheetSelected = null;
    questionSelected = null;
    questionPartSelected = null;

    document.getElementById("q").innerHTML = "";
    document.getElementById("p").innerHTML = "";
    document.getElementById("percentageMarkDiv").innerHTML = "";
}


/**
 * Resets all appropriate buttons to questions and relevant variables
 */
function resetToQuestion(id) {
    isQuestionSelected = true;
    questionSelected = id;
    isQuestionPartSelected = false;
    questionPartSelected = null;

    document.getElementById("p").innerHTML = "";
    document.getElementById("percentageMarkDiv").innerHTML = "";
}


/**
 * Resets all appropriate buttons to worksheets and relevant variables
 */
function resetToWorksheet(id) {
    isWorksheetSelected = true;
    worksheetSelected = id;
    isQuestionSelected = false;
    questionSelected = null;
    isQuestionPartSelected = false;
    questionPartSelected = null;

    document.getElementById("q").innerHTML = "";
    document.getElementById("p").innerHTML = "";
    document.getElementById("percentageMarkDiv").innerHTML = "";
}


/**
 * Displays the percentage selection for markers to choose from.
 */
function showPercentageMarkSelection() {
    var html = "<label for=\"percentageMark\">Percentage mark:</label>\n" +
        "    <select class=\"form-control\" id=\"percentageMark\">\n" +
        "      <option>100</option>\n" +
        "      <option>90</option>\n" +
        "      <option>80</option>\n" +
        "      <option>70</option>\n" +
        "      <option>60</option>\n" +
        "      <option>50</option>\n" +
        "      <option>40</option>\n" +
        "      <option>30</option>\n" +
        "      <option>20</option>\n" +
        "      <option>10</option>\n" +
        "      <option>0</option>\n" +
        "    </select>";

    document.getElementById('percentageMarkDiv').innerHTML = html;
}


/**
 * Returns the mark selected in the percentage selection
 *
 * @returns {*}
 */
function getMarkGiven() {
    var markSelector = document.getElementById('percentageMark');
    var selectedOption = markSelector.options[markSelector.selectedIndex];

    return selectedOption.text;
}


/**
 * POSTs the sign-off data to be stored in the database
 *
 * @returns {boolean}
 */
function sendWorksheetSignOff() {
    var dataToPost = signOffChange;
    dataToPost = dataToPost.replace("wks", "");
    dataToPost = dataToPost.replace("q", "");
    dataToPost = dataToPost.replace("p", "");

    $.post('../scripts/DatabaseQueries.php', {submission: dataToPost, sendWorksheetSignOffKey: 1}, function (data) {
        if (data == "fail") {
            alert("The sign-off could not be sent.");
        }
        // prevents firefox warning about post data
        window.location=window.location;
    }).fail(function () {
        alert("The sign-off could not be sent.");
    });
    return false;
}


///////////////////////////// memcached ///////////////////////////////


/**
 * Removes a sign-off request from Memcached
 *
 * @param request
 * @returns {boolean}
 */
function removeRequest(request) {
    if (request !== "NA") {
        $.post('../scripts/MemcachedQueries.php', {
            queryToRemove: request,
            removeQueueEntryKey: 1
        }, function (data) {
            // See done.
        }).done(function (data) {
            if (data == "fail") {
                alert("Failed to remove request.");
            }
            // prevents firefox warning about post data
            window.location=window.location;
        }).fail(function () {
            alert("Failed to remove request.");
        });
        return false;
    } else {
        location.href = '../index.php';
    }
}


/**
 * Un-assigns the current demonstrator from a request
 *
 * @param request
 * @returns {boolean}
 */
function unassignRequest(request) {
    if (request !== "NA") {
        $.post('../scripts/MemcachedQueries.php', {
            queryToUnassign: request,
            unassignQueueEntryKey: 1
        }, function (data) {
            // See done.
        }).done(function (data) {
            if (data == "fail") {
                alert("The request could not be unassigned.");
            }
            location.href = '../index.php';
        }).fail(function () {
            alert("The request could not be unassigned.");
        });
        return false;
    } else {
        location.href = '../index.php';
    }
}