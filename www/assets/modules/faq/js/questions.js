/**
 * Created by E_dulentsov on 21.06.2017.
 */
document.addEventListener("DOMContentLoaded", function () {
var oProjects = "";
/*
 Дата тэйблс выводим список проектов, при этом сразу грузим только новые
 */

    var currentStatus = parseInt($('.TableProject').data('status'));

    if ($('.TableProject').length>0) {
        window.TableProject = $('.TableProject').DataTable({
            "ajax": "/faq/questions/getlist",
            "processing": true,
            "serverSide": true,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "ordering": false,
            "iDisplayLength": 10,
            rowId: 'id',
            "columns": [
                {
                    "data": "id"
                },
                {
                    "data": "user_id"
                },
                {
                    "data": "title"
                },
                {
                    "data": "status"
                },
                {
                    "data": "date"
                },
                {
                    "data": "control"
                }
            ],
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.10.9/i18n/Russian.json'
            }
        });
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

    $(document).on('submit', '.ProjectForm', function () {
        var id = $(this).find('[name=id]').val();
        var $this = $(this);
        $this.find('button').button('loading');
        $.ajax({
            type: "POST",
            url: '/faq/questions/save',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.error == "0") {
                    toastr.info('Успешно!');
                    $('.modal').modal('hide');
                    if(id>0) {window.TableProject.row("#" + id).remove().draw();} // если редактировали то удалим старую строку
                    var jsonRow = JSON.parse(res.jsonRow);
                    window.TableProject.row.add(jsonRow).draw();
                } else {
                    toastr.info(res.data);
                }
                $this.find('button').button('reset');
            }
        });
        return false;
    });

    $(document).on('click', '.delProject', function () {
        var element = $(this);
        var id = $(this).data('id');
        var TableProjectTRDel = $(this).parents('tr');
        if(confirm('Удалить?')){
            $.ajax({
                type: "POST",
                url: '/faq/questions/del/'+id,
                success: function (res) {
                    var res = JSON.parse(res);
                    if (res.error == "0") {
                        window.TableProject.row(TableProjectTRDel).remove().draw();
                        toastr.info(res.data);
                    } else {
                        toastr.info(res.data);
                    }
                }
            });
        }
        return false;
    });


});

});