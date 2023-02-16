document.addEventListener("DOMContentLoaded", function () {

    $(document).on('submit', '.RecaptchaForm', function () {
        var id = $(this).find('[name=id]').val();
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/recaptcha/save',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.error == "0") {
                    toastr.info('Успешно!');
                   window.location.reload();
                } else {
                    toastr.info(res.data);
                }
            }
        });
        return false;
    });

});