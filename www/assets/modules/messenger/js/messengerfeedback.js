/**
 * Created by E_dulentsov on 21.06.2017.
 */
document.addEventListener("DOMContentLoaded", function () {

    $(document).on('submit', '.MessengerFeedbackForm', function () {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/messenger/feedback/save',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.error == "0") {
                    $this.html('<h2>Спасибо! Ваше сообщение успешно отправлено.<h2>');
                } else {
                    $this.find('.MessengeError').show();
                    $this.find('.MessengeError').html('<h2>'+res.data+'<h2>');
                }
            }
        });
        return false;
    });

});