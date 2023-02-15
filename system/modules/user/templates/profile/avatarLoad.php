<?php
/**
 * Created by PhpStorm.
 * User: noutbuk
 * Date: 03.07.2018
 * Time: 20:34
 */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Загрузка аватара</h4>
</div>
<div class="modal-body">
    <div>
        <div id="drop-files" class="drop-files" ondragover="return false">
            <p class="for-image">Перетащите изображение сюда</p>
        </div>
        <form id="frm">
            <input type="file" id="uploadbtn"/>
        </form>
        <input type="hidden" id="x" name="x"/>
        <input type="hidden" id="y" name="y"/>
        <input type="hidden" id="w" name="w"/>
        <input type="hidden" id="h" name="h"/>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <button class="btn btn-large btn-inverse sendImg"  id="upload-button" disabled type="button">Сохранить</button>
</div>
