<? $model = $data['model']; ?>
<div class="col-sm-12">
    <form action="/faq/questions/save" method="post" enctype="multipart/form-data" class="ProjectForm">
        <div class="col-sm-12">
            <input type="hidden" name="id" value="<?= $model->id; ?>">
            <div class="col-sm-6">
                <label class="smalllabel">user_id</label>
                <? $name =  'user_id'; ?>
                <input type="text" name="<?= $name; ?>" value="<?= (isset($model->$name))?$model->$name:''; ?>" class="form-control input-sm">
            </div>
            <div class="col-sm-6">
                <label for="name" class="smalllabel">Статус</label>
                <? $name = 'status'; ?>
                <select class="form-control" name="<?= $name; ?>">
                    <?php
                    $SelectArr = \modules\faq\models\mQuestions::instance()->getStatusArr();
                    foreach ($SelectArr as $Key => $Name) {
                        $selected = '';
                        if(isset($model->$name) && $model->$name==$Key){
                            $selected = 'selected';
                        }
                        ?><option <?= $selected; ?> value="<?= $Key; ?>"><?= $Name; ?></option><?
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-12">
                <label class="smalllabel">Заголовок</label>
                <? $name =  'title'; ?>
                <input type="text" name="<?= $name; ?>" value="<?= (isset($model->$name))?$model->$name:''; ?>" class="form-control input-sm">
            </div>
            <div class="col-sm-12">
                <label class="smalllabel">Вопрос</label>
                <? $name =  'questions'; ?>
                <textarea class="form-control" name="<?= $name; ?>"><?= (isset($model->$name))?$model->$name:''; ?></textarea>
            </div>
        </div>
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success col-sm-12" style="margin-top: 15px;">сохранить</button>
        </div>
    </form>
</div>