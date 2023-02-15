<div class="modal-header" id="modalNewsHead">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="modalNewsTitle">Добавить/изменить категорию</h4>
</div>
<div class="modal-body" id="modalNewsBody" style="background-color: white;">
    <form action="/news/admin/savecategory" method="post" class="form-horizontal" id="newsCatForm"
          enctype="multipart/form-data">
        <label for="name">Название</label>
        <input type='text' name='name' value="<?= $data->name; ?>"
               class="form-control">
        <label for="alias">URL</label>
        <input type='text' name='alias' value="<?= $data->alias; ?>"
               class="form-control">
        <input type="hidden" name="id" value="<?= $data->id; ?>">
    </form>
</div>
<div class="modal-footer" id="modalNewsFooter" style="background-color: white;">
    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
    <button class='btn btn-success' id='sendAndStop' type='submit' onclick="send(document.getElementById('newsCatForm'));">Сохранить</button>
</div>