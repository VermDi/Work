$(document).ready(function () {

    // Html для выбранной формы
    $("#result .form-item").on('click', function (){
        let form_id = $(this).attr('data-id');
        $(".test-input").text(form_id);

        $.ajax({
            url: '/feedback/admin/listforms',
            type: 'GET',
            data: {
                form_id: form_id
            },
            error: function () {
                alert('Что-то пошло не так!');
            },
            success: function (data) {
                $("#form-html-wrap").html(data);
            }
        });

    });
});