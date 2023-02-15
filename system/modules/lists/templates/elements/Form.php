<? $model = $data['model']; ?>
<div class="col-sm-12">
    <form action="/lists/elements/save" method="post" enctype="multipart/form-data" class="ElementsForm">
        <input type="hidden" name="id" value="<?= $model->id; ?>">
        <input type="hidden" name="pid" value="<?= $data['pid']; ?>">
        <div class="col-sm-12">
            <div>
                <label class="smalllabel">Наименование</label>
                <? $name = 'name'; ?>
                <input type="text" name="<?= $name; ?>" value="<?= $model->$name; ?>" class="form-control input-sm">
            </div>
            <div>
                <label class="smalllabel">Ключ элемента</label>
                <? $name = 'key_item'; ?>
                <input type="text" name="<?= $name; ?>" value="<?= $model->$name; ?>" class="form-control input-sm">
            </div>
        </div>
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success col-sm-12" style="margin-top: 15px;">сохранить</button>
        </div>
    </form>
</div>