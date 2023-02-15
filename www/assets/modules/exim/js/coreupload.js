document.addEventListener("DOMContentLoaded", function () {

    $(document).on('submit', '.CoreUploadForm', function () {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/exim/coreupload/save',
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
                    $('.uploadServerCore').show();
                    //$('.tokenForm .resultTezt').html('Успешно установлен. <a href="javascript:window.location.reload()">обновить страницу</a>');
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

    $(document).on('click', '.uploadServerCore', function () {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: $this.attr('href'),
            success: function (res) {
                var res = JSON.parse(res);
                if (res.error == "0") {
                    $.toast({
                        heading: res.data,
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
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