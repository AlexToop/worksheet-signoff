var doneBootstrapClass = "btn-success";
var partialDoneBootstrapClass = "btn-warning";
var incompleteBootstrapClass = "btn-outline-secondary";


/**
 * Provides the html content for creating a button in a group
 *
 * @param buttonType
 * @param buttonNumber
 * @param classStructureArrays
 * @param signOffsList
 * @param selectedWorksheet
 * @param selectedQuestion
 * @returns String
 */
function getButtonHtml(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth) {
    var output = "";
    var foundInput = [];
    var disabledStatus = "";

    if (buttonType == "p") {
        foundInput = getPartButtonColourAndValue(signOffsList, selectedWorksheet, selectedQuestion, buttonNumber, lockedWorksheets);
    } else if (buttonType == "q") {
        foundInput = (isMaxDepth) ?
            getPartButtonColourAndValue(signOffsList, selectedWorksheet, (buttonType + buttonNumber), 1, lockedWorksheets) :
            getQuestionButtonColourAndValue(selectedWorksheet, buttonNumber, classStructureArrays, signOffsList);
    } else if (buttonType == "wks") {
        foundInput = (isMaxDepth) ?
            getPartButtonColourAndValue(signOffsList, (buttonType + buttonNumber), "q1", 1, lockedWorksheets) :
            getWorksheetButtonColourAndValue(buttonNumber, classStructureArrays, signOffsList, lockedWorksheets);
        disabledStatus = (foundInput[2]) ? "disabled" : "";
    } else {
        return "";
    }

    output += "<button type=\"button\" " + disabledStatus + " class=\"btn border-secondary signoffbuttons " + foundInput[0] + "\" id=\"" + buttonType;
    output += buttonNumber + "\">" + buttonNumber + " " + foundInput[1] + "</button>";
    return output;
}


/**
 * Gets an array of colour and value for part buttons
 *
 * @param signOffsList
 * @param selectedWorksheet
 * @param selectedQuestion
 * @param buttonNumber
 * @returns {Array}
 */
function getPartButtonColourAndValue(signOffsList, selectedWorksheet, selectedQuestion, buttonNumber, lockedWorksheets) {
    var output = [];
    var worksheetNo = getNoFromButtonId(selectedWorksheet, "wks");
    var questionNo = getNoFromButtonId(selectedQuestion, "q");
    var percentAchieved = getPartCompletionPercentage(worksheetNo, questionNo, buttonNumber, signOffsList);

    if (percentAchieved == 100) {
        output[0] = doneBootstrapClass;
    } else {
        output[0] = (percentAchieved > 0) ? partialDoneBootstrapClass : incompleteBootstrapClass;
    }

    output[1] = percentAchieved + "%";
    output[2] = (lockedWorksheets.length > 0 && lockedWorksheets.includes(worksheetNo)) ? true : false;
    return output;
}


/**
 * Gets an array of colour and value for question buttons
 *
 * @param selectedWorksheet
 * @param buttonNumber
 * @param classStructureArrays
 * @param signOffsList
 * @returns {Array}
 */
function getQuestionButtonColourAndValue(selectedWorksheet, buttonNumber, classStructureArrays, signOffsList) {
    // No need to check for locked worksheets here because users should not be shown the results of this
    // function if the worksheet is locked.
    var output = [];
    var colour = getQuestionColour(getNoFromButtonId(selectedWorksheet, "wks"), buttonNumber, classStructureArrays, signOffsList);

    output[0] = colour;
    output[1] = getButtonValueForColour(colour);
    return output;
}


/**
 * Gets an array of colour and value for worksheet buttons
 *
 * @param buttonNumber
 * @param classStructureArrays
 * @param signOffsList
 * @returns {Array}
 */
function getWorksheetButtonColourAndValue(buttonNumber, classStructureArrays, signOffsList, lockedWorksheets) {
    var output = [];
    var colour = getWorksheetColour(buttonNumber, classStructureArrays, signOffsList);

    output[0] = colour;
    output[1] = getButtonValueForColour(colour);
    output[2] = (lockedWorksheets.length > 0 && lockedWorksheets.includes(buttonNumber));
    return output;
}


/**
 * Determines the value to display for a given button colour class.
 *
 * @param colour
 * @returns {string}
 */
function getButtonValueForColour(colour) {
    if (colour == doneBootstrapClass) {
        return "<i class='fa fa-check'></i>";
    }
    if (colour == partialDoneBootstrapClass) {
        return "<i class='fa fa-minus'></i>";
    }
    return "<i class='fa fa-times'></i>";
}


/**
 * Determines the bootstrap colour class of a worksheet
 *
 * @param worksheetNo
 * @param databaseStructure
 * @param signOffsList
 * @returns {string}
 */
function getWorksheetColour(worksheetNo, databaseStructure, signOffsList) {
    var noOfQuestionsToCheck = databaseStructure[worksheetNo - 1].length;
    var noComplete = 0;

    for (var questionNo = 1; questionNo < (noOfQuestionsToCheck + 1); questionNo++) {
        var questionResult = getQuestionColour(worksheetNo, questionNo, databaseStructure, signOffsList);

        if (questionResult == doneBootstrapClass) {
            noComplete++;
        } else if (questionResult == partialDoneBootstrapClass) {
            noComplete += 0.5;
        }
    }

    if (noComplete == noOfQuestionsToCheck) {
        return doneBootstrapClass;
    }
    return (noComplete > 0) ? partialDoneBootstrapClass : incompleteBootstrapClass;
}


/**
 * Determines the bootstrap colour class of a question
 *
 * @param worksheetNo
 * @param questionNo
 * @param databaseStructure
 * @param signOffsList
 * @returns {string}
 */
function getQuestionColour(worksheetNo, questionNo, databaseStructure, signOffsList) {
    var noOfPartsToCheck = databaseStructure[worksheetNo - 1][questionNo - 1];
    var percentageComplete = 0;

    for (var currPart = 1; currPart < (noOfPartsToCheck + 1); currPart++) {
        percentageComplete += getPartCompletionPercentage(worksheetNo, questionNo, currPart, signOffsList);
    }

    var totalPercentagePossible = noOfPartsToCheck * 100;
    if (percentageComplete == totalPercentagePossible) {
        return doneBootstrapClass;
    }
    return (percentageComplete == 0) ? incompleteBootstrapClass : partialDoneBootstrapClass;
}


/**
 * Determines the percentage achieved for a question part
 *
 * @param worksheetNo
 * @param questionNo
 * @param partNo
 * @param signOffsList
 * @returns {number}
 */
function getPartCompletionPercentage(worksheetNo, questionNo, partNo, signOffsList) {
    var percentageComplete = 0;

    for (var signOff = 0; signOff < signOffsList.length; signOff++) {
        if ((worksheetNo == signOffsList[signOff][0]) &&
            (questionNo == signOffsList[signOff][1]) &&
            (partNo == signOffsList[signOff][2])) {
            percentageComplete += signOffsList[signOff][3];
        }
    }
    return percentageComplete;
}


/**
 * Determines the number of a button from it's typeId
 *
 * @param buttonId
 * @param type
 * @returns {number}
 */
function getNoFromButtonId(buttonId, type) {
    var strippedString = buttonId.replace(type, "");
    var strippedInt = parseInt(strippedString);
    return strippedInt;
}

module.exports = getButtonHtml;