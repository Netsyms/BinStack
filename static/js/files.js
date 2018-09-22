/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

$(document).on('change', ':file', function () {
    var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});

$(':file').on('fileselect', function (event, numFiles, label) {
    var message = numFiles > 1 ? numFiles + ' files selected' : label;
    $("#uploadstatus").val(message);
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
})