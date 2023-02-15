/**
 * Created by Евгения on 20.06.2017.
 */
var $document = $(document);
$document.on('click', '.delete-permission', function () {
    if (confirm('Вы уверены что хотите удалить право?')) {
        location.href = '/user/permission/delete/' + $(this).data('id')
    }
})