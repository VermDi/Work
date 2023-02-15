document.addEventListener("DOMContentLoaded", function () {


    $(document).on('click', '.delf', function () {
        $(this).parents('.folder-row').remove();
        return false;
    });

    $(document).on('click', '.addFolder', function () {

        $('.folders').append('<div class="folder-row">' + $('.folders .folder-row').html() + '</div>');
        return false;
    });

    $(document).on('submit', '.manifestForm', function () {
        var $this = $(this);
        var NameModule = $(this).data('id');
        $.ajax({
            type: "POST",
            url: '/exim/manifest/save/' + NameModule,
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
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
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

    $(document).on('click', '.chooseDirectory', function () {
        var $this = $(this);
        removeDirectoryBlock();
        $this.parent().append('<div class="chooseDirectoryBlock">' +
            '' +
            '<p><b>Выберите файл или директорию</b> <a class="closeDirectoryBlock btn btn-danger btn-xs" href="#"><i class="fa fa-close"></i></a></p>' +
            '<p><b><a href="#" class="setPath">Вставить</a></b> <span class="path-current"></span></p>' +
            '<div class="dirBlockLoad"></div>' +
            '</div>');
        dirBlockLoad('');
        return false;
    });

    $(document).on('click', '.curFile', function () {
        var $this = $(this);
        var razd = '';
        if ($('.chooseDirectoryBlock .path-current').html()) {
            razd = '/';
        }
        var path = $('.chooseDirectoryBlock .path-current').html() + razd + $this.html();

        dirBlockLoad(path);
        return false;
    });

    $(document).on('click', '.setPath', function () {
        $(this).parents('.folder-row').find('input').val($('.chooseDirectoryBlock .path-current').html());
        return false;
    });
    $(document).on('click', '.closeDirectoryBlock', function () {
        removeDirectoryBlock();
        return false;
    });

    function removeDirectoryBlock() {
        $('.chooseDirectoryBlock').remove();
    }

    function dirBlockLoad(path) {
        var DataForm = new FormData();
        DataForm.append('path', path);

        var http = new XMLHttpRequest();
        http.open("POST", '/exim/manifest/getfolders');
        http.onreadystatechange = function () {//Call a function when the state changes.
            if (http.readyState == 4 && http.status == 200) {
                var res1 = JSON.parse(http.responseText);
                if (res1.error == 0) {
                    $('.chooseDirectoryBlock .path-current').html(res1.path);
                    var TextList = '';
                    for (var key in res1.files) {
                        // этот код будет вызван для каждого свойства объекта
                        // ..и выведет имя свойства и его значение

                        //console.log( "Ключ: " + key + " значение: " + res1.files[key] );
                        TextList = TextList + '<div class="setFile"><a class="curFile" href="#">' + res1.files[key] + '</a></div>';
                    }
                    $('.chooseDirectoryBlock .dirBlockLoad').html(TextList);

                } else {
                    alert(res1.data);
                }
            }
        }
        http.send(DataForm);
    }


});