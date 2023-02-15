/**
 * Created by Женя on 28.03.2018.
 */
$(function () {
    var $modal = $('#myModal');
    var $document = $(document);
    var tableProperties = $('.table').DataTable({
        'language': {
            'url': "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Russian.json"
        },
        "columns": [
            {"data": "id"},
            {"data": "name"},
            {"data": "title"},
            {"data": "description"},
            {"data": "type"},
            {"data": "buttons"}
        ],
        "serverSide": true,
        ajax: function (data, callback, settings) {
            $.ajax({
                'url': "/user/properties/list",
                'type': 'POST',
                data: data
            }).done(function (response) {
                //Данный вариант покажет ошибку если она есть, и при этом не будет фатала, что позволит работать коду дальше
                try {
                    var res = JSON.parse(response);
                } catch (e) {
                    console.log(response);
                    return false;
                }

                callback({
                    draw: res.draw,
                    data: res.data,
                    recordsTotal: res.recordsTotal,
                    recordsFiltered: res.recordsFiltered
                });
            });
        },

    });
    $modal.on('show.bs.modal', function (e) {
        var target = $(e.relatedTarget);
        $(this).find('.modal-content').load(target.attr('data-action'));

    });

    $modal.on('shown.bs.modal', function () {
        var allowedRegex = /^[a-zA-Z0-9\_]+$/;
        var allowedCharRegex = /[a-zA-Z0-9\_]/;
        $document.on('paste', "#name", function (e) {
            console.log('here');
            var newValue = e.originalEvent.clipboardData.getData('Text');
            if (!allowedRegex.test(newValue)) {
                e.stopPropagation();
                return false;
            }
        });

        $document.on('keypress', "#name", function (e) {
            return allowedCharRegex.test(e.key);
        });
    });
    $document.on('click', '.save-property', function () {
        var formValid = true;
        var $form = $("#form-property");
        $form.find('input').each(function () {
            var formGroup = $(this).parents('.form-group');
            if (this.checkValidity()) {
                formGroup.addClass('has-success').removeClass('has-error');
                formGroup.find('.help-block').text('')
            } else {
                formGroup.addClass('has-error').removeClass('has-success');
                formGroup.find('.help-block').text('Поле не может быть пустым');
                formValid = false;
            }
        });

        function checkUnique() {
            return $.ajax({
                'url': '/user/properties/checkunique',
                type: 'POST',
                data: {name: $form.find('#name').val(),'id':$form.find('#id').val()}
            }).pipe(function (response) {
                return response
            })
        }

        //если форма валидна, то
        if (formValid) {
            checkUnique().done(function (r) {
                if (r === "NotUnique") {
                    $form.find('#name').parents('.form-group').addClass('has-error').removeClass('has-success');
                    $form.find('#name').parents('.form-group').find('.help-block').text('Такое имя уже используется');
                } else {
                    var data = new FormData($form.get(0));
                    $.ajax({
                        'url': '/user/properties/save',
                        'type': 'POST',
                        'data': data,
                        'dataType': 'json',
                        cache: false,
                        processData: false, // Не обрабатываем файлы (Don't process the files)
                        contentType: false, // Так jQuery скажет серверу что это строковой запрос
                        success: function (response) {
                            if (response.status === 'OK') {
                                $modal.modal('hide');
                                tableProperties.draw();
                            } else {
                                console.log('error:' + response.message);
                            }

                        },
                        error: function (response) {
                            console.log('error:' + response.message);
                        }
                    })
                }
            })


        }

    });


    $document.on('click', '.delete-property', function () {
        var id = $(this).data('id');
        if (confirm('Удалить свойство?')) {
            $.ajax({
                'url': '/user/properties/delete',
                'type': 'POST',
                'data': {id: id},
                'dataType': 'json',
                success: function (response) {
                    if (response.status === 'OK') {
                        tableProperties.draw();
                    } else {
                        console.log('error:' + response.message);
                    }

                },
                error: function (response) {
                    console.log('error:' + response.message);
                }
            })
        }
    });

    $document.on('keydown', 'input, textarea', function () {
        if ($(this).parents('.form-group').hasClass('has-error')) {
            $(this).parents('.form-group').removeClass('has-error');
            $(this).next('.help-block').text('');
        }
    });
    $document.on('change', 'select', function () {
        if ($(this).parents('.form-group').hasClass('has-error')) {
            $(this).parents('.form-group').removeClass('has-error');
            $(this).next('.help-block').text('');
        }
    });

});