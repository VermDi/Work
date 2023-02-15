$(document).on('submit', '.form-amocrm', function () {
    /*
   Сохранение настроек CRM
     */
    event.preventDefault();
    let DataForm = new FormData(this);
    $.ajax({
        url: '/amocrm/admin/settings',
        type: 'POST',
        data: DataForm,
        processData: false,
        contentType: false,
        success: function (data) {
            data = JSON.parse(data)
            alert(data.message)
        }
    });
});

$("img").click(function (e) {
    let el = e.target
    if (el.classList[0] === 'image_hover'){
        $(this).removeClass('image_hover')
    }else {
       $(this).addClass('image_hover')
    }
});