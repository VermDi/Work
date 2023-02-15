/**
 * Created by Евгения on 13.09.2017.
 */
var scanButton = $('button[data-action="scan"]');
var scanAddButton = $('button[data-action="scanAdd"]');
var createMigrationButton = $('button[data-action="migrationCreate"]');
var scanMigrations = function () {

    scanButton.trigger('scan.migrations.start');
    $.get('/migrations/scan', function (response) {
        scanButton.trigger('scan.migrations.success', response);
    })
};
var scanAddMigrations = function (migrations) {
    scanAddButton.trigger('scan.add.migrations.start');
    $.post('/migrations/scan', migrations, function (response) {
        scanAddButton.trigger('scan.add.migrations.success', response);
    })
};

var createMigration = function (data) {
    createMigrationButton.trigger('create.migration');
    $.ajax({
        'url': '/migrations/create',
        'type': 'POST',
        'data': data
    }).done(function (response) {
        if (response === 'OK') {
            modal.find('.result').addClass('alert alert-success');
            modal.find('.result').text('Миграция успешно создана. Можете закрыть окно.');
        }
    })
};
var modal = $('#migrationsModal');
scanButton
    .on('click', function () {
        scanMigrations()
    })
    .on('scan.migrations.start', function () {
        $(this).button('loading');
    })
    .on('scan.migrations.success', function (event, data) {
        $(this).button('reset');
        modal.find('.modal-body').html(data);
        modal.modal();
    });

scanAddButton.on('click', function () {
    scanAddMigrations();
})
    .on('scan.add.migrations.start', function () {
        $(this).button('loading');
    })
    .on('scan.add.migrations.success', function (event, data) {
        $(this).button('reset');
        modal.find('.modal-title').text('Результат выполнения миграций');
        var result = JSON.parse(data);
        $.each(result, function (idx, value) {
            modal.find('#' + idx).addClass(value.type);
            if (value.type === 'success') {
                modal.find('#' + idx).find('.result').html(value.message);
            }
            if (value.type === 'danger') {
                var errorHtml = "<span class='viewError'>Error</span><div class='error'>" + value.message + "</div>";
                modal.find('#' + idx).find('.result').html(errorHtml);
            }

        });
    });


modal.on('hidden.bs.modal', function () {
    location.href = '/migrations'
});

modal.on('shown.bs.modal', function () {
    $('#module').select2({
        'placeholder': 'Выберете модуль',
        allowClear: true
    });

    $('#datetimepicker1').datetimepicker({
        locale: 'ru',
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down",
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right'
        },
        timepicker:false,
        format:'d.m.Y'
    });
});

var $document = $(document);

$document.on('click', '.viewError', function () {
    $('.error').hide();
    $(this).parent().find('.error').toggle();
});

$document.on('click', '.migrationCreate', function () {
    createMigration({'name': $('#name').val(), 'module': $('#module').val()});
});

$document.on('click','.migrationsDownForm',function () {
    modal.modal({'remote':'migrations/downform'});

})
$document.on('click', '.migrationsDown', function () {
    var count=$('#count').val();
    if(parseInt(count) < 0 || count===''){
        count=1;
    }
    if (confirm('Вы точно хотите откатить миграции?')) {
        $.ajax({
            'url': '/migrations/down/'+count,
            'type': 'POST'
        }).done(function (response) {
            console.log(response);
            var result = JSON.parse(response);
            if (result.success) {
                location.reload();
            } else {
                $.each(result.error, function (idx, value) {
                    $('#errors').append('<div class="alert alert-danger">' + value + '</div>')
                })
            }
        })
    }
});


jQuery(function ($) {
    $(document).mouseup(function (e) { // событие клика по веб-документу
        var div = $(".error"); // тут указываем ID элемента
        if (!div.is(e.target) // если клик был не по нашему блоку
            && div.has(e.target).length === 0) { // и не по его дочерним элементам
            div.hide(); // скрываем его
        }
    });
});

$document.on('click', '.migrationCreateForm', function () {
    modal.modal({'remote': '/migrations/createform'});

});

$('.migrations').dataTable({
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

    }
});

// $.datetimepicker.setDateFormatter({
//     parseDate: function (date, format) {
//         var d = moment(date, format);
//         return d.isValid() ? d.toDate() : false;
//     },
//
//     formatDate: function (date, format) {
//         return moment(date).format(format);
//     }
// });
// $.datetimepicker.setLocale('ru');


