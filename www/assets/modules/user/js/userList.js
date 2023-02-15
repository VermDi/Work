/**
 * Created by Евгения on 13.06.2017.
 */
var $document = $(document);
var $tableUsers = $("#users");
let $tableUsers2 = $("#users2");

$document.ready(function () {
    $tableUsers.dataTable({
        'language': {
            "processing": "Подождите...",
            "search": "Поиск:",
            "lengthMenu": "Показать _MENU_ записей",
            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
            "infoEmpty": "Записи с 0 до 0 из 0 записей",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "infoPostFix": "",
            "loadingRecords": "Загрузка записей...",
            "zeroRecords": "Записи отсутствуют.",
            "emptyTable": "В таблице отсутствуют данные",
            "paginate": {
                "first": "Первая",
                "previous": "Предыдущая",
                "next": "Следующая",
                "last": "Последняя"
            },
            "aria": {
                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                "sortDescending": ": активировать для сортировки столбца по убыванию"
            }

        },
        'stateSave': true
    });
    $tableUsers2.dataTable({
        'language': {
            "processing": "Подождите...",
            "search": "Поиск:",
            "lengthMenu": "Показать _MENU_ записей",
            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
            "infoEmpty": "Записи с 0 до 0 из 0 записей",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "infoPostFix": "",
            "loadingRecords": "Загрузка записей...",
            "zeroRecords": "Записи отсутствуют.",
            "emptyTable": "В таблице отсутствуют данные",
            "paginate": {
                "first": "Первая",
                "previous": "Предыдущая",
                "next": "Следующая",
                "last": "Последняя"
            },
            "aria": {
                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                "sortDescending": ": активировать для сортировки столбца по убыванию"
            }

        },
        'stateSave': true
    });
    let table = $tableUsers.DataTable();
   // let table2 = $tableUsers.DataTable();
    $tableUsers.find('tbody').on('click', 'tr td:nth-child(2)', function () {
        var data = table.row(this).data();
        location.href = '/user/tree/' + data[0];
    });
});

var $modal = $('#source-modal');

$modal.on('show.bs.modal', function (e) {
    var target = $(e.relatedTarget);
    $(this).find('.modal-content').load(target.attr('data-action'));
    $(this).find('.modal-dialog').addClass('modal-lg');
});


