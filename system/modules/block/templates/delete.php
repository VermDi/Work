<?php
/**
 * @var $data array
 */
?>
<div class="panel panel-body">
<ol class="breadcrumb">
    <li><a href="/admin">Управление</a></li>
    <li><a href="/block">Блоки</a></li>
    <li>Удаление</li>
</ol>
<form action="/block/delete/<?=$data['item']->id?>" method="post" id="deleteBlockForm">
    <div id="deleteBlockErrors"></div>
    <div class="alert alert-warning">
        Удалить блок #<?=$data['item']->id?>?
    </div>
    <a href="/block" class="btn btn-default">Отмена</a>
    <button autocomplete="off" data-loading-text="Удаление..." type="submit" class="btn btn-danger submit">Удалить</button>
</form>
	</div>
