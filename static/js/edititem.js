$("#cat").easyAutocomplete({
    url: "action.php",
    ajaxSettings: {
        dataType: "json",
        method: "GET",
        data: {
            action: "autocomplete_category"
        }
    },
    preparePostData: function (data) {
        data.q = $("#cat").val();
        return data;
    },
    getValue: function (element) {
        return element.name;
    },
    list: {
        onSelectItemEvent: function () {
            var catid = $("#cat").getSelectedItemData().id;
            $("#realcat").val(catid).trigger("change");
        },
        match: {
            enabled: true
        }
    }
});
$("#loc").easyAutocomplete({
    url: "action.php",
    ajaxSettings: {
        dataType: "json",
        method: "GET",
        data: {
            action: "autocomplete_location"
        }
    },
    preparePostData: function (data) {
        data.q = $("#loc").val();
        return data;
    },
    getValue: function (element) {
        return element.name;
    },
    list: {
        onSelectItemEvent: function () {
            var locid = $("#loc").getSelectedItemData().id;
            $("#realloc").val(locid).trigger("change");
        },
        match: {
            enabled: true
        }
    }
});

$("#assignedto").easyAutocomplete({
    url: "action.php",
    ajaxSettings: {
        dataType: "json",
        method: "GET",
        data: {
            action: "autocomplete_user"
        }
    },
    preparePostData: function (data) {
        data.q = $("#assignedto").val();
        return data;
    },
    getValue: function (element) {
        return element.username;
    },
    template: {
        type: "custom",
        method: function (value, item) {
            return item.name + " <i class=\"small\">" + item.username + "</i>";
        }
    }
});

$('#name').on('input propertychange paste', function() {
    $('#name_title').text($('#name').val());
});