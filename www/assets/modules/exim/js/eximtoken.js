document.addEventListener("DOMContentLoaded", function () {

    $(document).on('submit', '.tokenForm', function () {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/exim/savetoken',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.error == "0") {
                    $.toast({
                        heading: res.data,
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
                    //$('.tokenForm .resultTezt').html('Успешно установлен. <a href="javascript:window.location.reload()">обновить страницу</a>');
                    document.querySelector('.tokenForm').innerHTML = '<h2>Успешно установлен. <a href="javascript:window.location.reload()">обновить страницу</a></h2>';
                    setTimeout(function () {
                        window.location.reload();
                    }, 5000);
                }
                if (res.error == "1") {
                    $.toast({
                        heading: res.data,
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
                }
            }
        });
        return false;
    });

    $(document).on('click', '.removeToken', function () {

        if(confirm('Удалить токен')) {
            $.ajax({
                type: "POST",
                url: '/exim/removetoken',
                success: function (msg) {
                    var result = JSON.parse(msg);
                    if (result.error == 0) {
                        $("[name=token]").val('');
                        $.toast({
                            heading: 'Успешно!',
                            text: result.data,
                            position: 'top-right',
                            icon: 'success'
                        });
                        window.location.reload();
                    } else {

                        $.toast({
                            heading: 'Внимание!',
                            text: result.data,
                            position: 'top-right',
                            icon: 'error'
                        });
                    }
                }
            });
        }

        return false;
    });

    $(document).on('click', '.CreateWWW', function () {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/exim/savewww',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.error == "0") {
                    $.toast({
                        heading: res.data,
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
                    $this.parent().html('Успешно. <a href="javascript:window.location.reload()">обновить страницу</a>');
                }
                if (res.error == "1") {
                    $.toast({
                        heading: res.data,
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
                }
            }
        });
        return false;
    });

});