$('#name').on('input propertychange paste', function() {
    $('#name_title').text($('#name').val());
});