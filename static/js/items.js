$('#itemtable').DataTable({
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "<i class=\"fa fa-cube fa-fw\"></i> " + data[2];
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
        dataFilter: function (data) {
            var json = jQuery.parseJSON(data);
            json.data = [];
            json.items.forEach(function (row) {
                json.data.push([
                    "",
                    row.editbtn,
                    row.name,
                    row.catname,
                    row.locname + " (" + row.loccode + ")",
                    row.code1,
                    row.code2,
                    row.qty,
                    row.username
                ]);
            });
            return JSON.stringify(json);
        }
    }
});