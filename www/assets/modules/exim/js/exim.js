document.addEventListener("DOMContentLoaded", function () {
    var oProjects = "";
    /*
     Дата тэйблс выводим список проектов, при этом сразу грузим только новые
     */

    var currentStatus = parseInt($('.TableModules').data('status'));

    if ($('.TableModules').length > 0) {
        window.TableModules = $('.TableModules').DataTable({
            "ajax": "/exim/getlist",
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "iDisplayLength": 50,
            rowId: 'id',
            "columns": [
                {
                    "data": "id"
                },
                {
                    "data": "manifest"
                },
                {
                    "data": "version"
                },
                {
                    "data": "info"
                },
                {
                    "data": "version_description"
                },
                {
                    "data": "control"
                }
            ],
            "language": {
                "url": "/assets/vendors/datatables/datatables.ru/datatables_ru.json"
            },
            "drawCallback": function( settings ) {
                checkModules();
            }
        });
    }

    $(document).ready(function () {
        $(document).on('click', '.ajax', function () {
            //e.preventDefault(); //отключаем подъем
            // Получаем данные по ссылке
            var vis = $(this);
            var href = vis.attr('href');
            var title = vis.data('title');
            $.ajax({
                type: "POST",
                url: href,
                success: function (msg) {
                    $("#ContentTitle").html(title);
                    $("#AjaxContent").html(msg);
                    $("#Form").modal('show');
                    if (typeof LoadForm != 'undefined') {
                        LoadForm();
                    }
                }
            });
            return false;
        });

        $(document).on('click', '.CreateExim', function () {
            var $this = $(this);
            var NameModule = $(this).data('id');
            $.ajax({
                type: "POST",
                url: '/exim/createexim/' + NameModule,
                success: function (res) {
                    var res = JSON.parse(res);
                    if (res.error == "0") {
                        $.toast({
                            heading: res.data,
                            text: '',
                            position: 'top-right',
                            icon: 'info'
                        });
                        window.location.reload();
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


        $(document).on('click', '.delModule', function () {
            var $this = $(this);
            var NameModule = $(this).data('id');
            if (confirm('Удалить?')) {
                $.ajax({
                    type: "POST",
                    url: '/exim/delmodule/' + NameModule,
                    success: function (res) {
                        var res = JSON.parse(res);
                        if (res.error == "0") {
                            $.toast({
                                heading: res.data,
                                text: '',
                                position: 'top-right',
                                icon: 'info'
                            });
                            window.location.reload();
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
            }
            return false;
        });


        $(document).on('submit', '.moduleForm', function () {
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: '/exim/savemodule',
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
                        $this.html(res.message);
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

        $(document).on('click', '.uploadServer', function () {
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
                        window.location.reload();
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


    // Проверим модули на сервере
    function checkModules() {
        if(document.getElementsByClassName('TableModules').length>0){

            var ArrModules = [];
            var TrTableArr = document.querySelectorAll(".TableModules tbody tr"), i;
            for (i = 0; i < TrTableArr.length; ++i) {
                TrTableArr[i].style.color = "green";
                var id = TrTableArr[i].getAttribute('id');
                //console.log(id);
                if(id){
                    //console.log(TrTableArr[i]);
                    ArrModules.push(id);
                }
            }
            // Проверим модуль на сервере
            checkModule(ArrModules);
        }
    }

    // Проверим модуль на сервере
    function checkModule(ArrModules) {

        let ModulesData = new FormData();
        ModulesData.append('Modules', JSON.stringify(ArrModules)); // добавляем поле

        var http = new XMLHttpRequest();
        http.open("POST", '/exim/checkserver');
        http.onreadystatechange = function () {//Call a function when the state changes.
            if (http.readyState == 4 && http.status == 200) {
                if (http.responseText.length < 3) { return;}
                var res2 = JSON.parse(http.responseText);

                if(res2.error == 0){
                    res2.info.forEach(function (res1, i, DataAndwerArr) {
                        //console.log(res1);
                        if(res1.nameModule){
                            if(res1.data){
                                document.querySelector("#"+res1.nameModule+" .infoServer").innerHTML = res1.data;
                            }
                            if (res1.error == 1) {
                                document.querySelector("#"+res1.nameModule+" .infoServer").style.color = "red";
                            }
                            if (res1.error == 0) {
                                document.querySelector("#"+res1.nameModule+" .infoServer").style.color = "green";
                            }
                        } else {

                        }
                    });
                } else {
                    alert(res2.data);
                }
                /*if (res1.error == 0) {
                    document.querySelector("#"+id+" .infoServer").innerHTML = 'Загрузите версию модуля на удаленный сервер, <a class="btn btn-info btn-xs" href="#instruction">инструкция</a>';
                    document.querySelector("#"+id+" .infoServer").style.color = "red";
                    //console.log('Загрузите модуль '+id);
                } else {
                    document.querySelector("#"+id+" .infoServer").innerHTML = 'Версия модуля есть на удаленном сервере';
                    document.querySelector("#"+id+" .infoServer").style.color = "green";
                    //console.log('Уже есть '+id);
                }*/
            }
        }
        http.send(ModulesData);
    }

});