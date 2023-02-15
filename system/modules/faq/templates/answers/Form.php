<? $model = $data['model']; ?>
<div class="col-sm-12">
    <form action="/faq/answers/save" method="post" enctype="multipart/form-data" class="ProjectForm">
        <div class="col-sm-12">
            <input type="hidden" name="id" value="<?= $model->id; ?>">
            <div class="col-sm-6">
                <label class="smalllabel">user_id</label>
                <? $name =  'user_id'; ?>
                <input type="text" name="<?= $name; ?>" value="<?= (isset($model->$name))?$model->$name:\modules\user\models\USER::current()->id; ?>" class="form-control input-sm">
            </div>
            <div class="col-sm-6">
                <label class="smalllabel">faq_questions_id</label>
                <? $name =  'faq_questions_id'; ?>
                <input type="text" name="<?= $name; ?>" value="<?= (isset($model->$name))?$model->$name:$data['q_id']; ?>" class="form-control input-sm">
            </div>
            <div class="col-sm-6">
                <label for="name" class="smalllabel">Статус</label>
                <? $name = 'status'; ?>
                <select class="form-control" name="<?= $name; ?>">
                    <?php
                    $SelectArr = \modules\faq\models\mAnswers::instance()->getStatusArr();
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
            <div class="col-sm-6">
                <? $name = 'best'; ?>
                <label class="smalllabel" style="padding-top: 15px;">
                    <input type="checkbox" value="1" name="<?= $name; ?>" <?= (!empty($model->$name))?'checked':''; ?> >
                    Лучший ответ</label>
            </div>
            <div class="col-sm-12">
                <label class="smalllabel">Ответ</label>
                <? $name =  'answer'; ?>
                <textarea class="form-control" name="<?= $name; ?>"><?= (isset($model->$name))?$model->$name:''; ?></textarea>
            </div>
        </div>
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success col-sm-12" style="margin-top: 15px;">сохранить</button>
        </div>
    </form>
</div>