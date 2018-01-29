/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

var itemtable = $('#itemtable').DataTable({
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "<i class=\"fas fa-cube fa-fw\"></i> " + data[2];
                }
            }),
            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                tableClass: 'table'
            }),
            type: "column"
        }
    },
    columnDefs: [
        {
            targets: 0,
            className: 'control',
            orderable: false
        },
        {
            targets: 1,
            orderable: false
        },
        {
            targets: 8,
            orderable: false
        }
    ],
    order: [
        [2, 'asc']
    ],
    serverSide: true,
    ajax: {
        url: "lib/getitemtable.php",
        data: function (d) {
            if (filter == "stock") {
                d.show_want = 1;
            }
        },
        dataFilter: function (data) {
            var json = jQuery.parseJSON(data);
            json.data = [];
            json.items.forEach(function (row) {
                json.data.push([
                    "",
                    "<span class='btn-group'>" + row.editbtn + " " + row.clonebtn + "</span>",
                    row.name,
                    row.catname,
                    row.locname + " (" + row.loccode + ")",
                    row.code1,
                    row.code2,
                    row.qty,
                    row.want,
                    row.username
                ]);
            });
            return JSON.stringify(json);
        }
    }
});

$('#itemtable_filter').append("<span class=\"btn btn-default btn-sm mobile-app-show mobile-app-display\" onclick=\"scancode('#itemtable_filter label input');\"><i class=\"fas fa-barcode fa-fw\"></i></span>");

$(document).ready(function () {
    if (search_preload_content !== false) {
        var searchInput = $("#itemtable_filter label input");
        $(searchInput).val(search_preload_content);
        $(searchInput).trigger("input");
        $(searchInput).trigger("change");
    }
});