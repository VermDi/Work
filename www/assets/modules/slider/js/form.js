/**
 * Created by Евгения on 07.09.2017.
 */
var $document = $(document);
var $formModal = $("#FormModal");
$document.ready(function () {
    $('#photoimg').on('change', function () {
        var A = $("#imageloadstatus");
        var B = $("#imageloadbutton");
        $("#imageform").ajaxForm({
            target: '#preview',
            addingType:'reload',
            beforeSubmit: function () {
                A.show();
                B.hide();
            },
            success: function () {
                A.hide();
                B.show();
            },
            error: function () {
                A.hide();
                B.show();
            }
        }).submit();
    });
});

$document.on('mouseenter', '.gallery-container', function () {
    var $object = $(this);
    $object.find('.admin-toolbar').slideToggle();
});
$document.on('mouseleave', '.gallery-container', function () {
    var $object = $(this);
    $object.find('.admin-toolbar').slideToggle();
});
$document.on('click', '.change-position-up', function () {
    var id = $(this).parent().data('id');
    $.ajax({
        'type': 'POST',
        'url': '/slider/changeposition',
        'data': {
            'direction': 'up',
            'id': id,
            'type':$(this).data('type')
        }
    }).done(function (response) {
        $('#preview').html(response);
    })
});

$document.on('click', '.change-position-down', function () {
    var id = $(this).parent().data('id');
    $.ajax({
        'type': 'POST',
        'url': '/slider/changeposition',
        'data': {
            'direction': 'down',
            'id': id,
            'type':$(this).data('type')
        }
    }).done(function (response) {
        $('#preview').html(response);
    })
});

$document.on('click', '.slider-delete', function () {
    var id = $(this).parent().data('id');
    if (confirm('Вы точно хотите удалить этот слайд?')) {
        $.ajax({
            'type': 'POST',
            'url': '/slider/delete',
            data: {
                'id': id,
                'type':$(this).data('type')
            }
        }).done(function (response) {
            $('#preview').html(response);
        })
    }
});

$formModal.on('show.bs.modal', function (e) {
    var target = $(e.relatedTarget);
    $(this).find('.modal-title').html(target.data('title'));
    $(this).find('.modal-body').load(target.data('action'));
});


$formModal.on('hidden.bs.modal', function (e) {
    $(this).removeData('bs.modal');
});