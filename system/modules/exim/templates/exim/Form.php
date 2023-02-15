<div class="col-sm-12">
    <form action="/exim/save" method="post" enctype="multipart/form-data" class="eximForm">
        <input type="hidden" name="forcibly" value="<?= (isset($_REQUEST['forcibly']))?$_REQUEST['forcibly']:'' ?>" class="form-control input-sm">
        <div class="step1">
        <div class="col-sm-12">
            <div>
                <label for="name" class="smalllabel">Выберите zip файл модуля, для распаковки в корень CMS</label>
                <input type="file" name="file" class="input-sm">
            </div>
            <?php
            $disabled = '';
            if(!isset($data['supportSettings']->token)){
                $disabled = 'disabled="disabled"';
            }
            /*echo '<pre>';
            print_r($_REQUEST);
            echo '</pre>';*/
            ?>
            <div style="/*display: none;*/">
                <label for="name" class="smalllabel">Или введите ссылку на модуль <?= (!isset($data['supportSettings']->token))?'(токен не найден)':'' ?>, например <?= \modules\exim\helpers\EximHelper::instance()->getUrl() ?>/getmodule/gkg/2.0</label>
                <input <?= $disabled ?> type="text" name="url" value="<?= (isset($_REQUEST['install']))?$_REQUEST['install']:'' ?>" class="form-control input-sm">
            </div>
        </div>
        <div class="col-sm-12">
            <input type="submit" data-action="1" name="submit" class="btn btn-success col-sm-12 eximFormSubmit" value="Загрузить модуль" style="margin-top: 15px;">
        </div>
        </div>
        <div class="col-sm-12">
            <div class="resultTezt"></div>
        </div>
    </form>
</div>
<?php if(isset($_REQUEST['install'])){
    ?>
    <script>
        $('.eximForm').submit();
    </script>
    <?
} ?>