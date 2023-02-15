/**
 * Created by E_dulentsov on 21.06.2017.
 */
document.addEventListener("DOMContentLoaded", function () {
function Delete(obj) {

    // Получаем данные по ссылке
    var href = $(obj).attr('href');
    $.ajax({
        type: "POST",
        url: href,
        success: function (msg) {
            var res = JSON.parse(msg);
            if (res.error == "0") {
                window.messenger.row("#" + res.id).remove().draw();
            }
        }
    });
    return false;
}
var oProjects = "";
/*
 Дата тэйблс выводим список проектов, при этом сразу грузим только новые
 */

    var currentStatus = parseInt($('.TableMessenger').data('status'));

    if ($('.TableMessenger').length>0) {
        window.messenger = $('.TableMessenger').DataTable({
            "ajax": "/messenger/getlist/"+currentStatus,
            "processing": true,
            "serverSide": true,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "ordering": false,
            "iDisplayLength": 50,
            rowId: 'id',
            "columns": [
                {
                    "data": "id"
                },
                {
                    "data": "email"
                },
                {
                    "data": "create_at"
                },
                {
                    "data": "when_date_send"
                },
                {
                    "data": "date_send"
                },
                {
                    "data": "auto"
                },
                {
                    "data": "title"
                },
                {
                    "data": "text"
                },
                {
                    "data": "status"
                },
                {
                    "data": "control"
                }
            ],
            "language": {
                "url": "/assets/vendors/datatables/datatables.ru/datatables_ru.json"
            }
        });
    }
    $(document).on('init.dt', function () {
        /*
         Грузим форму добавления проекта
         */
        initAjax();

    });


    /*
     Инициируем аякс форму... Со всеми проверками
     */

function initAjax() {
    /*$(".ajax").on('click', function (e) {
        e.preventDefault(); //отключаем подъем
        // Получаем данные по ссылке
        var vis = $(this);
        var href = vis.attr('href');
        $.ajax({
            type: "POST",
            url: href,
            success: function (msg) {
                $("#ContentTitle").html(vis.data('title'));
                $("#AjaxContent").html(msg);
                $("#Form").modal('show');
                if (typeof LoadForm != 'undefined') {
                    LoadForm();
                }
            }
        });
    });*/
}

    $(document).on('click', '.LoadStatus', function () {
        var IdStatus = $(this).data('id');
        currentStatus = IdStatus;
        LoadStatus(IdStatus);
        return false;
    });

    function LoadStatus(IdStatus) {
        window.messenger.ajax.url("/messenger/getlist/"+IdStatus).load();
        GetCountStatus();
        window.history.pushState("", "", "/messenger/list/"+IdStatus);
    }

    /*
     Обновляем количества по статусам
     */
    function GetCountStatus() {
        $.ajax({
            'url': "/messenger/getcounts",
            'type': 'POST',
            'dataType': 'json',
            cache: false,
            processData: false, // Не обрабатываем файлы (Don't process the files)
            contentType: false, // Так jQuery скажет серверу что это строковой запрос
            success: function (response) {
                if (response.error == 0) {
                    var arrCounts = response.TaskCounts;
                    $.each(arrCounts, function( index, value ) {
                        $('.statusCount_'+index).html(value);
                        //alert( index + ": " + value );
                    });
                }
            },
            error: function (response) {
                console.log('error:' + response);
            }
        })
    }

$( document ).ready(function() {
    $(document).on('click', '.ajax', function () {
        //e.preventDefault(); //отключаем подъем
        // Получаем данные по ссылке
        var vis = $(this);
        var href = vis.attr('href');
        $.ajax({
            type: "POST",
            url: href,
            success: function (msg) {
                $("#ContentTitle").html(vis.data('title'));
                $("#AjaxContent").html(msg);
                $("#Form").modal('show');
                if (typeof LoadForm != 'undefined') {
                    LoadForm();
                }
            }
        });
        return false;
    });

    $(document).on('submit', '.messengerForm', function () {
        var id = $(this).find('[name=id]').val();
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/messenger/save',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.error == "0") {
                    $.toast({
                        heading: 'Успешно!',
                        text: '',
                        position: 'top-right',
                        icon: 'success'
                    });
                    $('.modal').modal('hide');
                    if(id>0) {window.messenger.row("#" + id).remove().draw();} // если редактировали то удалим старую строку
                    var jsonRow = JSON.parse(res.jsonRow);
                    window.messenger.row.add(jsonRow).draw();
                    GetCountStatus();
                } else {
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

    $(document).on('click', '.sendMessage', function () {
        if(confirm("Отправить сообщение?")){
            var id = $(this).data('id');
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: '/messenger/send/'+id,
                data: new FormData(this),
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.error == "0") {
                        $.toast({
                            heading: 'Успешно!',
                            text: '',
                            position: 'top-right',
                            icon: 'success'
                        });
                        $('.modal').modal('hide');
                        if(id>0) {window.messenger.row("#" + id).remove().draw();} // если редактировали то удалим старую строку
                        var jsonRow = JSON.parse(res.jsonRow);
                        window.messenger.row.add(jsonRow).draw();
                    } else {
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

    $(document).on('click', '.delMessenger', function () {
        var element = $(this);
        var id = $(this).data('id');
        var tableMessengerTRDel = $(this).parents('tr');
        $.ajax({
            type: "POST",
            url: '/messenger/del/'+id,
            success: function (data) {
                if(data==1) {
                    window.messenger.row(tableMessengerTRDel).remove().draw();
                    //reloadDataTable();
                    $.toast({
                        heading: 'Успешно!',
                        text: '',
                        position: 'top-right',
                        icon: 'success'
                    });
                    GetCountStatus();
                }
            }
        });
        return false;
    });
});

});