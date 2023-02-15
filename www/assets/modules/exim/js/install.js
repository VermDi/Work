document.addEventListener("DOMContentLoaded", function () {
    var actionSubmit = 1;
    $(document).on('click', '.eximFormSubmit', function () {
        actionSubmit = $(this).data('action');
    });

    $(document).on('click', '.createBackup', function () {
        var $this = $(this);
        $this.button('loading');
        var http2 = new XMLHttpRequest();
        http2.open("POST", '/backups/backup/save?comments=exim&db=1&files=1&exclude_folder=www/public/');
        http2.onreadystatechange = function () {//Call a function when the state changes.
            if (http2.readyState == 4 && http2.status == 200) {
                var res2 = http2.responseText;
                var res = JSON.parse(res2);
                if (res.error == "0") {
                    alert(res.data);
                } else {
                    alert(res.data);
                }
                $this.button('reset');
            }
        }
        http2.send();

        return false;
    });

    $(document).on('submit', '.eximForm', function () {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/exim/save/' + actionSubmit,
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
                    $('.eximForm .resultTezt').html(res.message);
                    /*$('.modal').modal('hide');
                     if(id>0) {window.TableModules.row("#" + id).remove().draw();} // если редактировали то удалим старую строку
                     var jsonRow = JSON.parse(res.jsonRow);
                     window.TableModules.row.add(jsonRow).draw();*/

                    if(actionSubmit==2){

                        var http = new XMLHttpRequest();
                        http.open("POST", '/migrations/up/progress');
                        http.onreadystatechange = function () {//Call a function when the state changes.
                            if (http.readyState == 4 && http.status == 200) {
                                var res1 = http.responseText;
                                if (res1 == 'OK' || res1 == '') {
                                    $('.eximForm .resultTezt').html('<div class="alert alert-primary" role="alert"><h3>Модуль успешно установлен</h3></div>');
                                } else {
                                    $('.eximForm .resultTezt').html(res1);
                                }
                            }
                        }
                        http.send();


                    }
                }
                if (res.error == "2") {
                    $this.find('.step1').hide();
                    $.toast({
                        heading: res.data,
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
                    $('.eximForm .resultTezt').html(res.message);
                }
                if (res.error == "1") {
                    //$this.find('.step1').hide();
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


    $(document).on('click', '.installModule', function () {
        var $this = $(this);
        var install = $(this).data('install-url');
        var forcibly = $(this).data('forcibly');
        $.ajax({
            type: "POST",
            url: '/exim/form',
            data: {install:install,forcibly:forcibly},
            success: function (msg) {
                $("#ContentTitle").html('Установить модуль');
                $("#AjaxContent").html(msg);
                $("#Form").modal('show');
                if (typeof LoadForm != 'undefined') {
                    LoadForm();
                }
            }
        });
        return false;
    });

    /*var params = window
        .location
        .search
        .replace('?','')
        .split('&')
        .reduce(
            function(p,e){
                var a = e.split('=');
                p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                return p;
            },
            {}
        );

    if(params['install']){
        ajaxclick($('.InstallModule').attr('href'), $('.InstallModule').data('title'));
    }*/

});