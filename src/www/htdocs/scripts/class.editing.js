var currSelectClassForGcEdit = 0;

/**
 * Fetches class group specific data and displays it in a modal on the page.
 *
 * @param classid
 * @param userType
 */
function editUsers(classid, userType) {
    $.post('../scripts/DatabaseQueries.php', {
        classIdForUsers: classid,
        groupForUsers: userType,
        returnAllUsersKey: 1
    }, function (data, classid) {
        // See done.
    }).done(function (data) {
        if (data == "fail") {
            alert("Could not retrieve users data.")
        } else {
            createEditMenu(userType, JSON.parse(data), classid);
        }
    }).fail(function () {
        alert("Could not retrieve users data.");
    });
}


/**
 * Displays the edit users modal with relevant information.
 *
 * @param typeNo
 * @param users
 * @param classid
 */
function createEditMenu(typeNo, users, classid) {
    var idPrefix = "";

    if (typeNo == 1) {
        $("#titleEditModal").text("Students");
        idPrefix = "removeStudent";
    } else if (typeNo == 2) {
        $("#titleEditModal").text("Demonstrators");
        idPrefix = "removeDemonstrator";
    } else if (typeNo == 3) {
        $("#titleEditModal").text("Lecturers");
        idPrefix = "removeLecturer";
    } else if (typeNo == 4) {
        $("#titleEditModal").text("Admins");
        idPrefix = "removeAdmin";
    }

    document.getElementById("viewUsersModalBody").innerHTML = "";
    var htmlExtra = "";

    if (("" !== users) && users.length > 0) {
        htmlExtra = "Currently recorded users:<div>";
        for (var userIndex = 0; userIndex < users.length; userIndex++) {
            htmlExtra += "<a href='#' id='" + idPrefix + userIndex + "'>Remove user: " + users[userIndex]['userid'] + "</a></br>";
            addRemoveUserOnClick(userIndex, users, classid, idPrefix, typeNo);
        }
        htmlExtra += "</div>";
    }

    var addDemonstratorHtml = "<p>Add user (will use existing details if previously registered):</p>";
    addDemonstratorHtml += "<input type=\"text\" class=\"form-control inline\" id='addUserUsername' name='addUserUsername' placeholder='Username'>";
    addDemonstratorHtml += "<input type=\"text\" class=\"form-control inline\" id='addUserFirstname' name='addUserFirstname' placeholder='First name'>";
    addDemonstratorHtml += "<input type=\"text\" class=\"form-control inline\" id='addUserLastname' name='addUserLastname' placeholder='Last name'>";
    addDemonstratorHtml += "<button class='btn btn-secondary inline' id='addNot" + idPrefix + classid + "Submit'>Add</button>";
    htmlExtra += addDemonstratorHtml;
    addNewUserOnClick(classid, typeNo, idPrefix);

    document.getElementById('viewUsersModalBody').insertAdjacentHTML('beforeend', htmlExtra);
    $("#editUsersModal").modal();
}


/**
 * Creates click events that allow for the removal of users.
 *
 * @param userIndex
 * @param users
 * @param classid
 * @param idPrefix
 * @param typeNo
 */
function addRemoveUserOnClick(userIndex, users, classid, idPrefix, typeNo) {
    var idToWatch = '#' + idPrefix + userIndex;

    $(document).on("click", idToWatch, function (event) {
        $('#editUsersModal').modal('hide');
        $('#removeUserModal').modal('show');

        var text = "Please confirm you wish to remove: " + users[userIndex]['userid'] + ". Note: " +
            "Any existing marks this user has received are backed up and will be restored if the " +
            "username is added back to the class.";
        document.getElementById('removeUserModalBody').innerHTML = text;

        $(document).on("click", '#removeStudentButton', function (event) {
            removeUser(userIndex, users, classid, typeNo);
            $('#removeUserModal').modal('show');
        });
    });
}


/**
 * Creates a click event for adding new users
 *
 * @param classid
 * @param type
 * @param idPrefix
 */
function addNewUserOnClick(classid, type, idPrefix) {
    var addButton = "#addNot" + idPrefix + classid + "Submit";

    $(document).on("click", addButton, function (event) {
        var username = $('#addUserUsername').val();
        var firstname = $('#addUserFirstname').val();
        var lastname = $('#addUserLastname').val();

        addUser(username, firstname, lastname, classid, type);
    });
}


/**
 * Performs the post call to php to send off a new user.
 *
 * @param username
 * @param firstname
 * @param lastname
 * @param classid
 * @param type
 * @returns {boolean}
 */
function addUser(username, firstname, lastname, classid, type) {

    $.post('../scripts/DatabaseQueries.php', {
        username: username,
        firstname: firstname,
        lastname: lastname,
        classid: classid,
        type: type,
        addUserKey: 1
    }, function (data) {
        // See done.
    }).done(function (data) {
        if (data == "fail") {
            alert("New user could not be added.");
        }
        alert("The user was added.");
        window.location.reload(true);
        // location.reload();
    }).fail(function () {
        alert("New user could not be added.");
    });
    return false;
}


/**
 * Performs post to php to remove a user.
 *
 * @param userIndex
 * @param users
 * @param classid
 * @param typeNo
 * @returns {boolean}
 */
function removeUser(userIndex, users, classid, typeNo) {
    $.post('../scripts/DatabaseQueries.php', {
        classid: classid,
        userid: users[userIndex]['userid'],
        typeNo: typeNo,
        removeUserKey: 1
    }, function (data) {
        // See done.
    }).done(function (data) {
        if (data == "fail") {
            alert("The deletion could not be completed. Note: You cannot remove yourself from classes.");
        }
        // location.reload();
        window.location.reload(true);
    }).fail(function () {
        alert("The deletion could not be completed. Note: You cannot remove yourself from classes.");
    });
    return false;
}


/**
 * Returns an array of information associated with a module from the data storage
 *
 * @param classid
 * @returns {boolean}
 */
function getArrayOfModuleData(classid) {
    $.post('../scripts/DatabaseQueries.php', {classid: classid, getClassExportDataKey: 1}, function (data) {
        // See done.
    }).done(function (data) {
        if (data == "fail") {
            alert("Issue exporting the class data from stored marks.");
        } else {
            try {
                data = JSON.parse(data);
            } catch (error) {
                alert(error);
            }
            doExport(data);
        }
    }).fail(function () {
        alert("Issue exporting the class data from stored marks.");
    });
    return false;
}


/**
 * Exports percentage results with lock dates included for a class
 *
 * @param classid
 * @returns {boolean}
 */
function getPercentagesLockData(classid) {
    $.post('../scripts/DatabaseQueries.php', {classid: classid, getPercentagesExportDataKey: 1}, function (data) {
        // See done.
    }).done(function (data) {
        if (data == "fail") {
            alert("Issue exporting the class data from stored marks.");
        } else {
            try {
                data = JSON.parse(data);
            } catch (error) {
                alert(error);
            }
            doExport(data);
        }
    }).fail(function () {
        alert("Issue exporting the class data from stored marks.");
    });
    return false;
}


/**
 * Shows the upload dialogue for amending the grade centre document with student marks
 *
 * @param classid
 */
function amendGcUpload(classid) {
    currSelectClassForGcEdit = classid;
    $('#uploadModel').modal('show');
    document.getElementById('gradeCenterFileUpload').addEventListener('change', processGradeCenterDocument);
}


/**
 * Function is based various discussions on the following StackOverflow thread:
 * https://stackoverflow.com/questions/27254735/filereader-onload-with-result-and-parameter
 * No one particular solution was used and solution elements were modified.
 *
 * @param input
 */
function processGradeCenterDocument(input) {
    var file = input.target.files[0];

    if (file) {
        var fileReader = new FileReader();

        fileReader.onload = function (e) {
            var contents = e.target.result;
            var output = [];
            var lines = contents.split("\n");

            for (var currentLine = 0; currentLine < lines.length; currentLine++) {
                var lineContentsArray = lines[currentLine].split(",");
                output[currentLine] = lineContentsArray;
            }
            addMarksToGradeCenterDocument(output);
        };

        fileReader.readAsText(file);
    } else {
        alert("Failed to load file");
    }
}


/**
 * Sends a post request to php to obtain the class mark details, then the details are added to the
 * data from the received gc document.
 *
 * @param gradeCenterDoc
 * @returns {boolean}
 */
function addMarksToGradeCenterDocument(gradeCenterDoc) {

    $.post('../scripts/DatabaseQueries.php', {
        classid: currSelectClassForGcEdit,
        getClassExportDataKey: 1
    }, function (data) {
        // See done.
    }).done(function (data) {
        if (data == "fail") {
            alert("Issue exporting the class data from stored marks.");
        } else {
            try {
                data = JSON.parse(data);
            } catch (error) {
                alert(error);
            }
            gradeCenterDoc = getMatchedMarksCSVFile(gradeCenterDoc, data);
            doExport(gradeCenterDoc);
            $('#uploadModel').modal('hide');
        }
    }).fail(function () {
        alert("Issue exporting the class data from stored marks.");
    });
    return false;
}


/**
 * Receives the class marks and the grade centre document and returns the grade centre document with
 * the marks added.
 *
 * @param gradeCenterDoc
 * @param exportedClass
 * @returns {*}
 */
function getMatchedMarksCSVFile(gradeCenterDoc, exportedClass) {
    // able to use exact indexes as exportedClass has been created within this system itself
    for (var currStudentIndex = 1; currStudentIndex < exportedClass.length; currStudentIndex++) {
        var studentRow = getStudentRowIndex(exportedClass[currStudentIndex][2], gradeCenterDoc);

        if (studentRow) {
            for (var currWorksheetMarkIndex = 3; currWorksheetMarkIndex <= exportedClass[0].length; currWorksheetMarkIndex++) {
                var columnToAppendMarkTo = getMarkIndex(gradeCenterDoc[0], exportedClass[0][currWorksheetMarkIndex]);
                if (columnToAppendMarkTo) {
                    gradeCenterDoc[studentRow][columnToAppendMarkTo] = exportedClass[currStudentIndex][currWorksheetMarkIndex];
                }
            }
        }
    }
    return gradeCenterDoc;
}


/**
 * Finds the index location of a mark (i.e. WK1) in a row given
 *
 * @param row
 * @param nameOfMark
 * @returns {*}
 */
function getMarkIndex(row, nameOfMark) {
    // try to find an exact match.
    for (var columnIndex = 0; columnIndex < row.length; columnIndex++) {
        if (row[columnIndex] == nameOfMark) {
            return columnIndex;
        }
    }
    // try to find an approximate match.
    try {
        nameOfMark = nameOfMark.split(" ")[0];
        for (var columnIndex = 0; columnIndex < row.length; columnIndex++) {
            var columnString = row[columnIndex].split(" ")[0];

            if (columnString == nameOfMark) {
                return columnIndex;
            }
        }
    } catch (error) {
        // ignore, as we don't mind if the string if not separable with spaces
    }
    return false;
}


/**
 * Obtains the row that a username is located in the grade centre document.
 *
 * @param username
 * @param gradeCenterDoc
 * @returns {*}
 */
function getStudentRowIndex(username, gradeCenterDoc) {
    for (var rowIndex = 1; rowIndex <= gradeCenterDoc.length; rowIndex++) {
        if (gradeCenterDoc[rowIndex].indexOf(username) != -1) {
            return rowIndex;
        }
    }
    return false;
}


/**
 * Provides a modal dialogue when attempting to delete a module and deals with the click event
 *
 * @param classid
 * @param moduleCode
 */
function deleteModuleDialog(classid, moduleCode) {
    $('#deleteModule').modal('show');

    var text = "Confirm deleting the class: " + moduleCode + ". Note: All related data will be deleted and this " +
        "class can be recreated if needed.";
    document.getElementById('removeModuleModalBody').innerHTML = text;

    $(document).on("click", '#removeModuleButton', function (event) {
        deleteModule(classid);
    });
}


/**
 * Posts php to delete a module from the data storage.
 *
 * @param classid
 * @returns {boolean}
 */
function deleteModule(classid) {
    $.post('../scripts/DatabaseQueries.php', {moduleToDelete: classid, deleteModuleKey: 1}, function (data) {
        // See done.
    }).done(function (data) {
        if (data == "fail") {
            alert("Could not delete module.");
        }
        window.location.reload(true);
    }).fail(function () {
        alert("Could not delete module.");
    });
    return false;
}


/**
 * Starts a download on the users browser with the data provided.
 * Credit to Arne H. Bitubekk for creation. The solution was minorly edited to use
 * more verbose naming conventions.
 *
 * Source: https://stackoverflow.com/questions/14964035/how-to-export-javascript-array-info-to-csv-on-client-side?rq=1
 *
 * @param data
 */
function doExport(data) {
    var csvContent = '';
    data.forEach(function (infoArray, index) {
        dataString = infoArray.join(',');
        csvContent += (index < data.length) ? (dataString + '\n') : dataString;
    });

    // The download function takes a CSV string, the filename and mimeType as parameters
    var download = function (content, fileName, mimeType) {
        var hyperlinkElement = document.createElement('a');
        mimeType = mimeType || 'application/octet-stream';

        if (navigator.msSaveBlob) { // IE10
            navigator.msSaveBlob(new Blob([content], {
                type: mimeType
            }), fileName);
        } else if (URL && 'download' in hyperlinkElement) { //html5 A[download]
            hyperlinkElement.href = URL.createObjectURL(new Blob([content], {
                type: mimeType
            }));
            hyperlinkElement.setAttribute('download', fileName);
            document.body.appendChild(hyperlinkElement);
            hyperlinkElement.click();
            document.body.removeChild(hyperlinkElement);
        } else {
            location.href = 'data:application/octet-stream,' + encodeURIComponent(content); // only this mime type is supported
        }
    };
    download(csvContent, 'class_export.csv', 'text/csv;encoding:utf-8');
}