/**
 * Created by Евгения on 07.09.2017.
 */
var $document = $(document);

$('#photoimg').on('change', function () {
    var A = $("#imageloadstatus");
    var B = $("#imageloadbutton");
    $("#image_form").ajaxForm({
        target: '#preview',
        addingType: 'reload',
        beforeSubmit: function () {
            A.show();
            B.hide();
        },
        success: function (response, x) {
            $('#temp_id').val($(x).find('#temp').val());
            A.hide();
            B.show();
        },
        error: function () {
            A.hide();
            B.show();
        }
    }).submit();
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
        'url': '/gallery/changeposition',
        'data': {
            'direction': 'up',
            'id': id,
            'type': $(this).data('type')
        }
    }).done(function (response) {
        $('#preview').html(response);
    })
});

$document.on('blur', '.image-title', function () {
    var text = $(this).text();
    if (text !== '') {
        $.ajax({
            'url': '/gallery/savetitle',
            'type': 'POST',
            'data': {
                id: $(this).data('id'),
                title: text
            }
        })
    }

});

$document.on('click', '.change-position-down', function () {
    var id = $(this).parent().data('id');
    $.ajax({
        'type': 'POST',
        'url': '/gallery/changeposition',
        'data': {
            'direction': 'down',
            'id': id,
            'type': $(this).data('type')
        }
    }).done(function (response) {
        $('#preview').html(response);
    })
});

$document.on('click', '.image-delete', function () {
    var id = $(this).parent().data('id');
    if (confirm('Вы точно хотите удалить это изображение?')) {
        $.ajax({
            'type': 'POST',
            'url': '/gallery/delete',
            data: {
                'id': id
            }
        }).done(function (response) {
            $('#preview').html(response);
        })
    }
});

$document.on('click', '.make-favorite', function () {
    $.ajax({
        'type': 'POST',
        'url': '/gallery/setmainimage',
        data: {
            id: $(this).data('id')
        }
    }).done(function (response) {
        $('#preview').html(response);
    })
});