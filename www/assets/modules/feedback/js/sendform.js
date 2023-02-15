document.addEventListener("DOMContentLoaded", function () {

    $(document).on('submit', '.FeedbackForm', function () {

        var $this = $(this);
        var tosend = $(this).data('tosend');
        var text_success = $(this).find('[name=text_success]').val();

        $.ajax({
            type: "POST",
            url: tosend,
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.error == "0") {
                    $this.html('<h2>'+text_success+'<h2>');
                    //$.fancybox.open(text_success);
                } else {
                    //$.fancybox.open(res.data);
                    $this.find('.MessengeError').show();
                    $this.find('.MessengeError').html('<h2>'+res.data+'<h2>');
                }
            }
        });
        return false;
    });

});