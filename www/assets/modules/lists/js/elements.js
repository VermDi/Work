document.addEventListener("DOMContentLoaded", function () {

    var pid = $('.TableElements').data('pid');
    if ($('.TableElements').length>0) {
        window.messenger = $('.TableElements').DataTable({
            "ajax": "/lists/lists/getlist/"+pid,
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
                    "data": "name"
                },
                {
                    "data": "key_item"
                },
                {
                    "data": "count_lists"
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

    $(document).on('submit', '.ElementsForm', function () {
        var id = $(this).find('[name=id]').val();
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: '/lists/elements/save',
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

    $(document).on('click', '.delLists', function () {
        var element = $(this);
        var id = $(this).data('id');
        if(confirm('Удалить?')){
            $.ajax({
                type: "POST",
                url: '/lists/lists/del/'+id,
                success: function (res) {
                    res = JSON.parse(res);
                    if (res.error == "0") {
                        $.toast({
                            heading: 'Успешно!',
                            text: '',
                            position: 'top-right',
                            icon: 'success'
                        });
                        window.messenger.row("#" + res.id).remove().draw();
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


});