$('#cattable').DataTable({
    columnDefs: [
        {
            targets: 0,
            orderable: false
        }
    ],
    order: [
        [1, 'asc']
    ]
});