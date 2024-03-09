$(function () {
    //ROTATED//
    $rotated = false;
    $("#navbarDropdown").hover(function () {
        if ($rotated == false) {
            $("#navigation_arrow").css({
                transform: "rotate(" + 90 + "deg)",
                transition: "all 0.5s ease 0s",
            });
            $rotated = true;
        } else {
            $("#navigation_arrow").css({
                transform: "rotate(" + 0 + "deg)",
                transition: "all 0.5s ease 0s",
            });
            $rotated = false;
        }
    });
    //END ROTATED

    //PASSWORD VIEW //
    $("body").on("click", ".password-control", function () {
        if ($("#password").attr("type") == "password") {
            $(this).addClass("view");
            $("#password").attr("type", "text");
        } else {
            $(this).removeClass("view");
            $("#password").attr("type", "password");
        }

        return false;
    });
    //END PASSWORD VIEW //

    $('[data-toggle="tooltip"]').tooltip();
});
