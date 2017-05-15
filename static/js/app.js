
$(document).ready(function () {
    /* Fade out alerts */
    $(".alert .close").click(function (e) {
        $(this).parent().fadeOut("slow");
    });
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