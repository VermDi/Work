/**
 * Created by Евгения on 20.06.2017.
 */
var $document = $(document);
$document.on('click', '.delete-role', function () {
    if (confirm('Вы уверены что хотите удалить роль?')) {
        location.href = '/user/role/delete/' + $(this).data('id')
    }
})