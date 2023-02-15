$(function () {
    function sendForm(data) {
        return $.ajax({
            url: '/install/sendform',
            'type': 'POST',
            dataType: 'json',
            cache: false,
            processData: false, // Не обрабатываем файлы (Don't process the files)
            contentType: false, // Так jQuery скажет серверу что это строковой запрос
            data: data
        }).pipe(function (p) {
            return p;
        });
    }

    var $document = $(document);
    $document.on('click', '#start_installation', function () {
        var btn = $(this);
        btn.prop('disabled', true);

        $('.result').text('');
        var form = $("#installation_form");
        var data = new FormData(form.get(0));
        sendForm(data).done(function (response) {
            if (typeof response.result !== "undefined" && response.result === 'OK') {
                setTimeout(function () {
                    location.href = '/migrations/up';
                },3000);

            } else {
                $(".result").text(response.message);
                btn.prop('disabled', false);
            }
        });
    });
});
