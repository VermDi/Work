<?php
/**
 * @var $data array
 */
?>
<div class="panel panel-body">
    <ol class="breadcrumb">
        <li><a href="/admin">Управление</a></li>
        <li><a href="/block">Блоки</a></li>
        <li>Добавление</li>
    </ol>
    <form action="/block/save/0/<?= $data['pid'] ?>" method="post" id="addBlockForm">
        <div class="form-group">
            <label for="block-name" class="control-label">код замены (рекомендуем писать на латинице)</label>
            <input type="text" data-name="name" required class="form-control" name="name" id="block-name">
        </div>
        <div class="form-group">
            <label for="block-title" class="control-label">Название (что описывает блок)</label>
            <input class="form-control" data-name="title" name="title" type="text" id="block-title">
        </div>
        <div class="form-group">
            <label for="block-content" class="control-label">Содержание</label>
            <textarea class="form-control" name="content" id="block-content" rows="10"></textarea>
        </div>
        <div class="checkbox">
            <label>
                <input style="position: absolute; margin-left: -20px;" type="checkbox" name="is_editor_enabled" id="toggleEditor"> Визуальный
                редактор
            </label>
        </div>
        <a href="/block" class="btn btn-default"  onclick="closeWindow(); return false;">Отмена</a>
        <button autocomplete="off" data-loading-text="Сохранение..." type="submit" class="btn btn-primary submit">
            Сохранить
        </button>
    </form>
</div>
<script>

   function wysw() {

        CKEDITOR.config.protectedSource.push(/<\?[\s\S]*?\?>/g);
        CKEDITOR.config.allowedContent = {
            script: true,
            $1: {
                // This will set the default set of elements
                elements: CKEDITOR.dtd,
                attributes: true,
                styles: true,
                classes: true
            }
        };
        var editor;
//        console.log(CKEDITOR.config);
        $('#toggleEditor').change(function () {
            if ($(this).is(':checked')) {
                editor = CKEDITOR.replace('block-content', {
                    height: 200,
                    filebrowserBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                    filebrowserUploadUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                    filebrowserImageBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=1&editor=ckeditor&fldr='
                });
            } else {
                editor.destroy();
            }
        })
	   $.fn.modal.Constructor.prototype.enforceFocus = function () {
		   modal_this = this
		   $(document).on('focusin.modal', function (e) {
			   if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
				   // add whatever conditions you need here:
				   &&
				   !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
				   modal_this.$element.focus()
			   }
		   });
	   };
    }
   wysw();
</script>

