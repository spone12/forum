jQuery(function () {
    $("#page_avatar_edit").on("click", function () {
        $("#user_avatar").click();
    });

    $("#user_avatar").change(function () {
        $("#form_change_avatar").submit();
    });
});

function generateApiKey() {
    $.ajax({
        url: "/profile/generate_api_key/",
        type: "PUT",
        dataType: "JSON",
        headers: {
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#apiKey").val(data.api_key);
            tata.success("", "Api ключ успешно установлен", {
                duration: 2000,
                animate: "slide",
                position: "tr",
                onClose: function () {},
            });
        },
        error: function (data) {
            var errors = data.responseJSON;
            console.log(data.responseText);
        },
    });
}

function edit_profile() {
    let name = $("#name_user").val();
    let gender = $("#gender").val();
    let town_user = $("#town_user").val();
    let date_user = $("#date_user").val();
    let about_user = $("#about_user").val();
    let user_id = $("#user_id").val();
    let phone = $("#phone_user").val();

    let data_send = {
        name,
        gender,
        town_user,
        date_user,
        about_user,
        user_id,
        phone,
    };

    $.ajax({
        url: "/profile/confirm_change/" + user_id,
        type: "PUT",
        data: {
            data_send: data_send,
        },
        dataType: "JSON",
        headers: {
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (!data.data_user.original.status) {
                let errorsHtml =
                    '<div class="alert alert-primary">' +
                    "<ul>" +
                    "<li>Вы не изменили ни одного поля</li>" +
                    "</ul>" +
                    "</div>";

                $("#form-errors").html(errorsHtml);
            } else {
                tata.success("", "Профиль успешно изменён", {
                    duration: 2000,
                    animate: "slide",
                    position: "tr",
                    onClose: function () {
                        window.location.href = "/profile";
                    },
                });
            }
        },
        error: function (data) {
            tata.error("", data.responseJSON.message, {
                duration: 2000,
                animate: "slide",
                position: "tr",
            });
        },
    });
}

function updateConfidentiality(id) {
    $("#modal_window_text").html(
        "<div class='row mb-3'><div class='col-12'><b>" +
            id +
            "</b>g</div></div>" +
            "<div class='row mb-0 align-items-center d-flex  justify-content-start'>" +
            "<div class='col-1'><input class='form-control' type='radio' name=" +
            id +
            " /></div><div class='col-10 '>Открыть для всех</div>" +
            "</div>" +
            "<div class='row mb-2 align-items-center d-flex justify-content-start'>" +
            "<div class='col-1'><input class='form-control' type='radio' name=" +
            id +
            " /></div><div class='col-10'>Скрыть для всех</div>" +
            "</div>" +
            "<div class='row align-items-center'><button class='col-12 btn btn-primary'>Готово</button></div>"
    );

    $("#modal_window").modal("show");
}
