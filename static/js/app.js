/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

$(document).ready(function () {
    /* Fade out alerts */
    $(".alert .close").click(function (e) {
        $(this).parent().fadeOut("slow");
    });

    if ($("#msg-alert-box").length) {
        $("#msg-alert-box .progress").css("height", "3px");
        $("#msg-alert-box .progress").css("border-radius", "0px 0px .25rem .25rem");
        $("#msg-alert-box .progress-bar").css("transition", "width 0.25s linear");
        var msginteractiontick = 0;
        var fifty = 10;
        var gone = 20;

        var msgticker = setInterval(function () {
            if ($('#msg-alert-box .alert:hover').length) {
                msginteractiontick = 0;
            } else {
                msginteractiontick++;
            }
            if (msginteractiontick > 0) {
                function setBarWidth(offset) {
                    $("#msg-alert-timeout-bar").css("width", (msginteractiontick + offset) / gone * 100 + "%");
                }
                setBarWidth(-1 + .25);
                setTimeout(function () {
                    setBarWidth(-1 + .5);
                }, 250);
                setTimeout(function () {
                    setBarWidth(-1 + .75);
                }, 500);
                setTimeout(function () {
                    setBarWidth(0);
                }, 750);
            } else {
                $("#msg-alert-timeout-bar").css("width", "0%");
            }

            if (msginteractiontick < fifty) {
                $("#msg-alert-box .alert").css("opacity", "1");
            }
            if (msginteractiontick == fifty) {
                $("#msg-alert-box .alert").fadeTo(1000, 0.5);
            }
            if (msginteractiontick >= gone) {
                setTimeout(function () {
                    if (msginteractiontick >= gone) {
                        $("#msg-alert-box .alert").fadeOut("slow");
                        window.clearInterval(msgticker);
                    }
                }, 1000);
            }
        }, 1000 * 1);

        $("#msg-alert-box").on("mouseenter", function () {
            $("#msg-alert-box .alert").css("opacity", "1");
            msginteractiontick = 0;
            console.log("👈😎👈 zoop");
        });
    }
});


/*
 * Remove feedback params from the URL so they don't stick around too long
 */
function getniceurl() {
    var url = window.location.search;
    url = url.substring(url.lastIndexOf("/") + 1);
    url = url.replace(/&?msg=([^&]$|[^&]*)/i, "");
    return url;
}
try {
    window.history.replaceState("", "", getniceurl());
} catch (ex) {

}