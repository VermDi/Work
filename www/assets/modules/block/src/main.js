$(document).ready(function () {
    window.BlockTable = $('#blockList-table').DataTable({
        'treeGrid': {
            'left': 10,
            'expandIcon': '<span>+</span>',
            'collapseIcon': '<span>-</span>'
        },
        "responsive": true,
        "processing": true,
        "ajax": "/block/table",
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "iDisplayLength": 50,
        rowId: 'id',
        "columns": [
            {
                title: '',
                target: 0,
                className: 'treegrid-control',
                data: function (item) {
                    if (item.children) {
                        return '<span>+<\/span>';
                    }
                    return '';
                }
            },
            {
                "data": "id"
            },
            {
                "data": "title"
            },
            {
                "data": "name"
            },
            {
                "data": "control"
            }

        ],
        "language": {
            "url": "/assets/vendors/datatables/datatables.ru/datatables_ru.json"
        }
    });
});

function setFormErrors(form, errors) {
    $.each(errors, function (name, value) {
        var field = form.find('[name=' + name + ']');
        var errors = $('<div class="label label-danger errors"></div>').appendTo(field.closest('.form-group'));
        $.each(value, function (i, text) {
            errors.append(text + "<br />");
        });
    });
}

function resetFormErrors(form) {
    form.find('.errors').remove();
}

function initForm() {
    // console.log(document.querySelector("#addBlockForm"));
    $('#addBlockForm, #editBlockForm').on('submit', function (e) {
        $('.submit').button('loading');
        e.preventDefault();
        var form = $(this);
        if (CKEDITOR) {
            for (instance in CKEDITOR.instances)
                CKEDITOR.instances[instance].updateElement();
        }
        $.ajax({
            url: form.prop('action'),
            data: form.serialize(),
            method: 'post',
            dataType: 'json',
            success: function (response) {
                resetFormErrors(form);
                if (response.errors) {
                    $('.submit').button('reset');
                    setFormErrors(form, response.errors);
                } else {
                    if (typeof response.lineData !== "undefined") {
                        if (window.BlockTable.row("#" + response.lineData.id).length > 0) {
                            window.BlockTable.row("#" + response.lineData.id).data(response.lineData.data);
                        } else {
                            window.BlockTable.row.add(response.lineData.data).draw();
                        }
                    }
                    $("#blocks-modal").modal('hide');
                }
            }
        });
    });

    $('#deleteBlockForm').on('submit', function (e) {
        $('.submit').button('loading');
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.prop('action'),
            method: 'post',
            dataType: 'json',
            success: function (response) {
                if (response.errors) {
                    var err = $('#deleteBlockErrors').empty();
                    $.each(response.errors, function (i, val) {
                        err.append('<p class="alert-alert-danger>' + val + '</p>');
                    });
                    $('.submit').button('reset');
                } else {
                    window.BlockTable.row("#" + response.id).remove().draw();
                    $("#blocks-modal").modal('hide');
                }
            }
        });
    });
}