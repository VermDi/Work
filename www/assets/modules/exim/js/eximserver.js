document.addEventListener("DOMContentLoaded", function () {

    var oProjects = "";
    /*
     Дата тэйблс выводим список проектов, при этом сразу грузим только новые
     */

    var currentStatus = parseInt($('.TableModulesServer').data('status'));

    if ($('.TableModulesServer').length > 0) {
        window.TableModulesServer = $('.TableModulesServer').DataTable({
            "ajax": "/exim/getlistserver",
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "iDisplayLength": 50,
            rowId: 'id',
            "columns": [
                {
                    "data": "id"
                },
                {
                    "data": "readme"
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

    if ($('.TableCoreServer').length > 0) {
        window.TableModulesServer = $('.TableCoreServer').DataTable({
            "ajax": "/exim/getlistserver/1",
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "iDisplayLength": 50,
            rowId: 'id',
            "columns": [
                {
                    "data": "id"
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

    $(document).ready(function () {
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

    });

});