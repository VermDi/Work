$(document).ready(function () {
    $('body').prepend('<div class="emind_admin_rules"></div>');
    $('.emind_admin_rules').load('/vizualeditor/save/loadpanel');


});
function clickandload(e) {
    if ($(e).prop("checked") == true) {
        load_editor();
    } else {
        unload_editor();
    }
}
function load_editor() {
    $("*[data-edit='emind_admin']").each(function (index) {
        $(this).addClass('editzone').attr("contenteditable", "true");
        CKEDITOR.disableAutoInline = true;
        CKEDITOR.inline(this, {
            height: 100,
            filebrowserBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
            filebrowserUploadUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
            filebrowserImageBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=1&editor=ckeditor&fldr='
        });
        $(this).on("blur", function () {
            var txt = "";
            var id = "";
            var table = "";
            var field = "";
            id = $(this).data('id');
            table = $(this).data('table');
            field = $(this).data('field');
            txt = $(this).html();

            $.ajax({
                url: '/vizualeditor/save',
                type: 'POST',
                data: {
                    table: table,
                    field: field,
                    id: id,
                    content: txt
                }
            }).done(function (res) {
                console.log(res);
            });
        });
    });
}

function unload_editor() {
    $("*[data-edit='emind_admin']").each(function (index) {
        $(this).removeClass('editzone').attr("contenteditable", "false");
        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].destroy(true);
        }
    });
}
function close_editor() {
    unload_editor();
    $('.emind_admin_rules').hide();
    $('body').css('padding-top', "0px");

}
