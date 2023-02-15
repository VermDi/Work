function show_wyswig() {
    if (CKEDITOR) {
        $('.ckeditor').ckeditor({
            disableNativeSpellChecker: false,// отключает запрет на стандартную проверку орфографии браузером
            extraPlugins: 'sourcedialog',
            removeButtons: 'source, Print,Form,TextField,Textarea,radio, checkbox,imagebutton,Button,SelectAll,Select,HiddenField',
            removePlugins: 'source, spellchecker, Form, TextField,Textarea, Button, Select, HiddenField , about, save, newpage, print,exportpdf, templates, scayt, flash, pagebreak, smiley,preview,find',
            filebrowserBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
            filebrowserUploadUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
            filebrowserImageBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=1&editor=ckeditor&fldr=',
        });
    } else {
        console.log('noEditor');
    }
}

document.getElementById('sendPageData').addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    let d = new FormData();
    d.append('content', JSON.stringify(builder.getData()));
    $.ajax({
        type: "POST",
        processData: false,
        contentType: false,
        url: '/pages/admin/saveFromFront/' + $(this).data('id'),
        data: d,
        success: function (msg) {
            var result = JSON.parse(msg);
            if (result.error) {
                alert("Ошибка!");
            } else {
                alert("Сохранено!");
            }
        }
    });

});
