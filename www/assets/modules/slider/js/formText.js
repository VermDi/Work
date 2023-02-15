/**
 * Created by Евгения on 08.09.2017.
 */
var $document=$(document);
var $formModal=$('#FormModal');
$formModal.on('show.bs.modal', function (e) {
    var target = $(e.relatedTarget);
    $(this).find('.modal-title').html(target.data('title'));
    $(this).find('.modal-body').load(target.data('action'));
});


$formModal.on('hidden.bs.modal', function (e) {
    $(this).removeData('bs.modal');
});