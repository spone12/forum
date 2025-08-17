/**
 * Give error message from response
 *
 * @param data
 * @param duration
 */
function errorMsgResponse(data, duration = 3000) {
    let errorMsg = "";
    let jsonResponse = data.responseJSON;

    if (typeof (jsonResponse.message) !== 'undefined') {
        errorMsg += jsonResponse.message;
    }

    if (typeof (jsonResponse.errors) !== 'undefined') {
        errorMsg += '<ul>';
        $.each(jsonResponse.errors, function(key, value) {
            errorMsg += '<li>' + value + '</li>';
        });
        errorMsg += "</ul>";
    }

    tata.error(errorMsg, "", {
        duration: duration,
        animate: "slide",
        position: "tr",
    });
}

/**
 *
 * @param message
 * @param title
 * @param duration
 * @param onClose
 */
function successMsg(message, title = "", duration = 2000, onClose = "") {
    tata.success(message, title, {
        duration: duration,
        animate: "slide",
        position: "tr",
        onClose: function () {
            if (onClose) {
                window.location.href = onClose;
            }
        },
    });
}
