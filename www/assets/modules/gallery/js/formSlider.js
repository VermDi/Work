/**
 * Created by Евгения on 08.09.2017.
 */
var $document = $(document);
$document.on('keyup', '.required', function () {
    if ($(this).val() !== '') {
        $('.save-button').removeAttr('disabled');
    } else {
        $('.save-button').attr('disabled', true);
    }
});
$document.ready(function () {
    var disabled = true;
    $('.required').each(function () {
        if ($(this).val() !== '' && disabled) {
            disabled = false;
        }
    });
    if (disabled) {
        $('.save-button').attr('disabled', true);
    } else {
        $('.save-button').removeAttr('disabled');
    }
});