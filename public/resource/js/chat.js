
var messagePage = 2;

function stopEditMessage() {
    $(".edit_msg_stop").hide();
    $("#dialog__message").text("").attr("isEdit", false);
    $(".dialog__send")
        .attr("src", "/img/chat/send_message.png")
        .attr("onclick", "sendMessage();");
}

function sendMessage() {
    let message = $.trim($(".dialog__message").html());
    let dialogWithId = $("#dialogWithId").val();
    let dialogId = $("#dialogId").val();

    if (jQuery.isEmptyObject(message)) {
        return;
    }

    $.ajax({
        url: "/chat/send_message",
        type: "POST",
        headers: {
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
        },
        async: false,
        data: {
            message: message,
            dialogWithId: dialogWithId,
            dialogId: dialogId,
        },
        success: function (data) {},
        error: function (data) {
            errorMsgResponse(data);
        },
    });
}

/**
 *
 * @param messageId
 */
function editMessage(messageId) {
    let message = $.trim($(".dialog__message").html());
    let dialogId = $("#dialogId").val();

    if (jQuery.isEmptyObject(message)) {
        return;
    }

    $.ajax({
        url: "/chat/edit_message",
        type: "PUT",
        headers: {
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
        },
        async: false,
        data: {
            message: message,
            messageId: messageId,
            dialogId: dialogId,
        },
        success: function (data) {
            $("#dialog__message").text("");
            $("#chatLs__chat-" + messageId)
                .find(".chatLs__text")
                .html(message);
            stopEditMessage();
        },
        error: function (data) {
            errorMsgResponse(data);
        },
    });
}

/**
 * Load AJAX messages
 */
function loadMessages() {
    split = $("#nextMessages").val().split("page=");
    $.ajax({
        url: split[0] + "page=" + messagePage,
        type: "GET",
        headers: {
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data.includes("chatLs__chat noMessages")) {
                $(".chatLs").off("scroll");
            } else {
                $(".chatLs").prepend($(data).find(".chatLs").children());
                messagePage++;
            }

            $(".loader").addClass("none");
        },
        error: function (data) {
            errorMsgResponse(data);
        },
    });
}

/**
 * Scroll down
 */
function scrollDown() {
    $(".chatLs").scrollTop($(".chatLs").prop("scrollHeight"));
}

$(document).ready(function () {
    scrollDown();

    /**
     * Event of loading new messages when scrolling to the top of the page
     */
    $(".chatLs").scroll(function () {
        const screenHeight = $(".chatLs").innerHeight();
        let scrolled = $(".chatLs").scrollTop() + screenHeight;

        if (screenHeight === scrolled) {
            $(".loader").removeClass("none");
            loadMessages();
            $(".chatLs").scrollTop(scrolled - screenHeight + 30);
        }
    });

    $(".dialog__scroll-down").click(function () {
        scrollDown();
    });

    /**
     * Message edit view function
     */
    $("body").on("click", "div[class=chatLs__move-edit]", function (e) {
        let messageId = $(this)
            .closest(".chatLs__chat")
            .attr("id")
            .split("chat-")[1];
        let message = $(this)
            .closest(".chatLs__chat")
            .find(".chatLs__text")
            .html();
        $("#dialog__message")
            .html(message)
            .attr("isEdit", true)
            .trigger("focus");
        $(".dialog__send")
            .attr("src", "/img/icons/edit.png")
            .attr("onclick", "editMessage(" + messageId + ");");
        $(".edit_msg_stop").show();
        $("html, body").animate(
            { scrollTop: $(document).height() - $(window).height() },
            300
        );
    });

    /**
     * Message delete function
     */
    $("body").on("click", "div[class=chatLs__move-delete]", function (e) {
        let dialogId = $("#dialogId").val();
        let mainBlock = $(this).closest(".chatLs__chat");
        let messageId = mainBlock.attr("id").split("chat-")[1];
        stopEditMessage();

        $.ajax({
            url: "/chat/delete_message",
            type: "DELETE",
            headers: {
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
            },
            async: false,
            data: {
                messageId: messageId,
                dialogId: dialogId,
            },
            success: function (data) {
                mainBlock
                    .addClass("delete_message")
                    .find(".chatLs__move-edit, .chatLs__move-delete")
                    .addClass("hide_message_btn");
                mainBlock.find(".chatLs__move-recover").removeClass("hide_message_btn");
            },
            error: function (data) {
                errorMsgResponse(data);
            },
        });
    });

    /**
     * Message recover function
     */
    $("body").on("click", "div[class=chatLs__move-recover]", function (e) {
        let dialogId = $("#dialogId").val();
        let mainBlock = $(this).closest(".chatLs__chat");
        let messageId = mainBlock.attr("id").split("chat-")[1];

        $.ajax({
            url: "/chat/recover_message",
            type: "PUT",
            headers: {
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
            },
            async: false,
            data: {
                messageId: messageId,
                dialogId: dialogId,
            },
            success: function (data) {
                mainBlock
                    .removeClass("delete_message")
                    .find(".chatLs__move-edit, .chatLs__move-delete")
                    .removeClass("hide_message_btn");
                mainBlock.find(".chatLs__move-recover").addClass("hide_message_btn");
            },
            error: function (data) {
                errorMsgResponse(data);
            },
        });
    });

    /**
     * Stop edit message
     */
    $(".edit_msg_stop").on("click", function () {
        stopEditMessage();
    });

    $("body").on("mouseover", ".chatLs__chat", function (e) {
        $(this).find(".chatLs__move-edit").show();
        $(this).find(".chatLs__move-delete").show();
    });
    $("body").on("mouseout", ".chatLs__chat", function (e) {
        $(this).find(".chatLs__move-edit").hide();
        $(this).find(".chatLs__move-delete").hide();
    });

    $(".search_chat").on("click", function () {
        if ($(this).attr("isQuery") == 0) return;

        $(this).attr("isQuery", 0).text("/");
        $("#chatSearch").val("");

        $(".Chat-search__item").not(".Chat-search__item:first").remove();
        $(".Chat-search").hide();
        $(".mainData").show();
    });

    $("#chatSearch").on("change", function () {
        var searchText = $.trim($(this).val());

        if (jQuery.isEmptyObject(searchText)) {
            $(".Chat-search__item").not(".Chat-search__item:first").remove();
            $(".Chat-search").hide();
            $(".mainData").show();
            return;
        }

        $.ajax({
            url: "/chat/search_all",
            type: "GET",
            headers: {
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
            },
            data: { searchText: searchText },
            success: function (data) {
                $(".mainData").hide();

                if (jQuery.isEmptyObject(data.searchResult[0])) {
                    $(".Chat-search__item:eq(0)")
                        .find(".Chat-search_body")
                        .html("Рузультатов поиска нет");
                    $(".Chat-search__item")
                        .not(".Chat-search__item:first")
                        .remove();
                    return;
                }

                $(".search_chat").attr("isQuery", 1).text("x");

                var chat = $(".Chat-search__item:eq(0)").clone();

                $(".Chat-search__item:eq(0)")
                    .find(".Chat-search_body")
                    .html(
                        "Результат поиска: " + data.searchResult.length + " элемент"
                    );
                $(".Chat-search__item")
                    .not(".Chat-search__item:first")
                    .remove();

                $.each(data.searchResult, function (key, searchItem) {
                    var elements = chat.clone();
                    elements
                        .find(".Chat-search_body")
                        .html(searchItem.text)
                        .attr("href", "/chat/dialog/" + searchItem.dialog_id);
                    elements
                        .find(".Chat-search__photo")
                        .attr("src", searchItem.avatar);
                    elements
                        .find(".Chat-search__link")
                        .text(searchItem.name)
                        .attr("href", "/profile/" + searchItem.id);

                    elements.appendTo(".Chat-search");
                });

                $(".Chat-search").show();
            },
            error: function (data) {
                errorMsgResponse(data);
            },
        });
    });

    // Chat
    if ($('.allDialogs').length === 0) {
        /**
         *  Pusher Chat Websocket
         */
        window, Echo.private('chat.' + $('#dialogId').val())
            .listen('ChatMessageEvent', (e) => {
                $("#dialog__message").text("");

                var newMessage = $(".chatLs__chat:eq(0)").clone();
                newMessage.attr("id", "chatLs__chat-" + e.messageObj.message_id);
                newMessage.find(".chatLs__text").html(e.messageObj.text);
                newMessage.find(".chatLs__photo").attr("src", e.userObj.avatar);
                newMessage.find(".chatLs__name").text(e.userObj.name);
                newMessage
                    .find(".chatLs__link")
                    .attr("href", "/profile/" + e.userObj.id);
                newMessage
                    .find(".chatLs__message-time")
                    .text(e.messageObj.created_at_hour)
                    .attr("title", "")
                    .attr("data-original-title", e.messageObj.difference);

                newMessage.appendTo(".chatLs");
                scrollDown();
            });

        /**
         * If press Shift + Enter -> run send or edit message function
         */
        const dialogMessage = document.getElementById("dialog__message");

        dialogMessage.addEventListener("keydown", function (e) {
            // Get the code of pressed key
            const keyCode = e.which || e.keyCode;

            // Don't generate a new line
            if (keyCode === 13 && !e.shiftKey) {
                e.preventDefault();
                if ($(dialogMessage).hasClass("dialog__message")) {
                    $(".dialog__send").click();
                }
            }
        });
    }
});
