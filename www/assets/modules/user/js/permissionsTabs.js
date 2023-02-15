$(document).on('click', '.check-all', function () {
    $(".permission-checkbox").each(function () {
        $(this).attr('checked',true);
    })
});
$(document).on('click', '.check-tab-permissions', function () {
    $(this).parents('.tab-pane').find(".permission-checkbox").each(function () {
        $(this).prop('checked',true);
    })
});