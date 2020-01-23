$("#nextButton").hide();

/**
 * checks the initial credentials given by the user
 */
$(document).on("click", "#initLoginButton", function (event) {
    var username = ($('#username').val() == "") ? null : $('#username').val();
    var password = ($('#password').val() == "") ? null : $('#password').val();

    if (username && password) {
        $.post('scripts/LoginRedirect.php', {
            username: username,
            password: password
        }, function (data) {
            // nothing currently.
        }).done(function (data) {

            if (data == "failed") {
                var html = "<div id=\"noModules\" class=\"alert alert-info\" " +
                    "role=\"alert\">User credentials were incorrect. Please retry or reset your password.</div>";
                document.getElementById("failedMessageArea").innerHTML = html;

            } else {
                document.getElementById("failedMessageArea").innerHTML = "";
                makeModuleSelector(data);
            }

        }).fail(function () {
            alert("Issue authenticating.");
        });
        return false;
    }
});


/**
 * Allows the users to select their class and locks other options.
 * provides information prompts if issues occur.
 *
 * @param modulesData
 */
function makeModuleSelector(modulesData) {
    modulesData = JSON.parse(modulesData);

    // if they are not registered for any classes
    if (modulesData == "") {
        $("#initLoginButton").hide();
        $("#username").prop('disabled', true);
        $("#password").prop('disabled', true);
        document.getElementById("passwordReset").innerHTML = "<div id=\"noModules\" class=\"alert alert-info\" " +
            "role=\"alert\">You are not registered for any modules currently. Please contact a member of staff with module access for assistance.</div>";
    } else {
        // provides details if they are registered for 1 or more classes
        $("#passwordReset").hide();
        $("#initLoginButton").hide();
        $("#username").prop('disabled', true);
        $("#password").prop('disabled', true);
        $("#nextButton").show();

        var html = "<label>Module</label> <select id='moduleDropdown' class=\"form-control bottom-margin-20px\">";
        for (var moduleIndex = 0; moduleIndex < modulesData.length; moduleIndex++) {
            var year = modulesData[moduleIndex][2] + "/" + (modulesData[moduleIndex][2] + 1);
            html += "<option value=\"" + modulesData[moduleIndex][0] + "\">" + modulesData[moduleIndex][1].toUpperCase() + ": " + year + "</option>";
        }
        html += "</select>";
        document.getElementById("showModules").innerHTML = html;
    }
}


/**
 * submits the student to the next page
 */
$(document).on("click", "#nextButton", function (event) {
    var username = ($('#username').val() == "") ? null : $('#username').val();
    var password = ($('#password').val() == "") ? null : $('#password').val();
    var classid = $('#moduleDropdown').children("option:selected").val();

    // makes a form to post without displaying the content to the users
    var url = 'scripts/Redirect.php';
    var form = $('<form action="' + url + '" method="post">' +
        '<input type="text" name="username" value="' + username + '" />' +
        '<input type="text" name="password" value="' + password + '" />' +
        '<input type="text" name="module" value="' + classid + '" />' +
        '</form>');
    $('body').hide();
    $('body').append(form);
    form.submit();
});


/**
 * looks for the enter button key presses to progress button selection
 */
$(document).bind('keypress', function (e) {
    if (e.keyCode == 13) {
        if ($('#nextButton').is(":hidden")) {
            $('#initLoginButton').trigger('click');
        }
        if ($('#initLoginButton').is(":hidden")) {
            $('#nextButton').trigger('click');
        }

    }
});