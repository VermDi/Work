<div class="modal-header" id="modalNewsHead">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="modalNewsTitle">Добавить/изменить статью</h4>
</div>
<div class="modal-body" id="modalNewsBody" style="background-color: white;">
    <form action="/news/admin/savearticle" method="post" class="form-horizontal" id="newsArticleForm"
          enctype="multipart/form-data">
        <div class="row">
            <div class="col-sm-12">
                <label for="name">Название статьи</label>
                <input type='text' name='name' value="<?= $data->name; ?>" class="form-control">
                <label for="alias">URL</label>
                <input type='text' name='alias' value="<?= $data->alias; ?>" class="form-control">
            </div>
            <div class="col-sm-6">
                <label for="data_create">Начало публикации</label>
                <input type='date' name='data_create' value="<?= $data->data_create; ?>" class="form-control">
            </div>
            <div class="col-sm-6">
                <label for="data_end">Конец публикации</label>
                <input type='date' name='data_end' value="<?= $data->data_end; ?>" class="form-control">
            </div>
            <div class="col-sm-12">
                <label for="short_article">краткое описание</label>
                <textarea name='short_article' class="form-control ckeditor"> <?= $data->short_article; ?></textarea>
            </div>
            <div class="col-sm-12">
                <label for="full_article">полное описание</label>
                <textarea name='full_article' class="form-control ckeditor"> <?= $data->full_article; ?></textarea>
            </div>
            <div class="col-sm-12">
                <label for="image">Загрузите картинку</label>
                <input type='file' name='image'>
                <?php if ($data->isImage()) {
                    ?><span id="ImgArticle"><?php $data->showImg(200, 200, true); ?></span>
                    <span class="btn btn-xs btn-danger" onclick="delImg(<?= $data->id; ?>, this);"><i
                                class="fa fa-trash"></i> удалить картинку</span>
                <?php } ?>
            </div>
            <div class="col-sm-12">
                <label for="title">SEO TITLE</label>
                <input type='text' name='title' value="<?= $data->title; ?>" class="form-control">
            </div>
            <div class="col-sm-6">
                <label for="meta_desc">meta DESCRIPTION</label>
                <input type='text' name='meta_desc' value="<?= $data->meta_desc; ?>" class="form-control">
            </div>
            <div class="col-sm-6">
                <label for="meta_keywords">meta KEYWORDS</label>
                <input type='text' name='meta_keywords' value="<?= $data->meta_keywords; ?>" class="form-control">
            </div>
            <div class="col-sm-12">
                <label for="visible">Видимость?</label>
                <input type='checkbox' name='visible' value="1" <?php if ($data->visible == 1) {
                    echo "checked";
                } ?>>
            </div>
            <input type="hidden" name="id" value="<?= $data->id; ?>">
            <input type="hidden" name="categories_id" value="<?= $data->categories_id; ?>">
        </div>
    </form>
</div>
<div class="modal-footer" id="modalNewsFooter" style="background-color: white;">
    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
    <button class='btn btn-success' id='sendAndStop' type='button'
            onclick="send(document.getElementById('newsArticleForm'));">Сохранить
    </button>
</div>