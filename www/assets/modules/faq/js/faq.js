$( document ).ready(function() {

    $(document).on('submit', '.faqForm', function () {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/faq/questionsave',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.error == "0") {
                    $('.faqForm').html(res.data);
                } else {
                    toastr.info(res.data);
                    if(document.getElementsByClassName('g-recaptcha').length){ grecaptcha.reset(); }
                }
            }
        });
        return false;
    });

    $(document).on('submit', '.answerForm', function () {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/faq/answersave',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.error == "0") {
                    $('.answerForm').html(res.data);
                } else {
                    toastr.info(res.data);
                    if(document.getElementsByClassName('g-recaptcha').length){ grecaptcha.reset(); }
                }
            }
        });
        return false;
    });

});