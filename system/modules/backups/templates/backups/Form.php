<? $model = $data['model']; ?>
<div class="col-sm-12">
    <form action="/backups/backup/save" method="post" enctype="multipart/form-data" class="ProjectForm">
        <div class="col-sm-12">
            <input type="hidden" name="id" value="<?= $model->id; ?>">
            <div>
                <label for="name" class="smalllabel">Комментарий</label>
                <input type="text" name="comments" value="<?= $model->comments; ?>" class="form-control input-sm">
            </div>
            <div style="    padding-top: 10px;">
                <label for="name" class="smalllabel"><input checked type="checkbox" name="db" value="1"> Копия БД</label>
            </div>
            <div style="    padding-top: 10px;">
                <label for="name" class="smalllabel"><input checked type="checkbox" name="files" value="1"> Копия Файлов</label>
            </div>
            <div>
                <label for="name" class="smalllabel">Папки для исключения через запятую без пробелов.</label>
                <input type="text" name="exclude_folder" value="www/public/" class="form-control input-sm">
            </div>
        </div>
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success col-sm-12" style="margin-top: 15px;">создать копию сайта</button>
        </div>
    </form>
</div>