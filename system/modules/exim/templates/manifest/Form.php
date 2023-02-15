<?php
/*echo '<pre>';
print_r($data);
echo '</pre>';*/
?>
<div class="col-sm-12">
    <form data-id="<?= (isset($data['EximConfig']['ModuleInfo']['name'])) ? $data['EximConfig']['ModuleInfo']['name'] : '' ?>"
          action="/exim/manifest/save" method="post" enctype="multipart/form-data" class="manifestForm">
        <div class="col-sm-12">
            <div>
                <label class="smalllabel">Версия</label>
                <?php $name = "version"; ?>
                <input type="text" name="<?= $name ?>" class="form-control input-sm"
                       value="<?= (isset($data['EximConfig'][$name])) ? $data['EximConfig'][$name] : '' ?>" required>
            </div>
        </div>
        <div class="col-sm-12">
            <div>
                <label class="smalllabel">Описание</label>
                <?php $name = "version_description"; ?>
                <textarea name="<?= $name ?>" class="form-control input-sm"
                          required><?= (isset($data['EximConfig']['ModuleInfo'][$name])) ? $data['EximConfig']['ModuleInfo'][$name] : '' ?></textarea>
            </div>
        </div>
        <div style="padding: 15px 0px;display: table;"><b>Папки</b></div>
        <div class="folders">
            <?php
            if (!empty($data['EximConfig']['Folders'])) {
                $fn = 0;
                foreach ($data['EximConfig']['Folders'] as $Folder) {
                    $fn++;
                    ?>
                    <div class="folder-row">
                        <div class="col-sm-12" style="padding-bottom: 15px;">
                            <input style="width: 90%;" type="text" name="Folders[]" class="form-control input-sm"
                                   value="<?= $Folder ?>">
                            <a data-title="Инфо" class="btn btn-info btn-xs chooseDirectory" href="#">/</a>
                            <a class="delf btn btn-danger btn-xs" href="#"><i class="fa fa-trash-o"></i></a><br>
                        </div>
                    </div>
                    <?
                }
            }
            ?>
        </div>
        <a class="btn btn-info btn-xs addFolder" href="#">Добавить</a>
        <div style="padding: 15px 0px;display: table;"><b>Выберите модули без которых не будет работать</b></div>
        <div class="col-sm-12">
            <div>
                <select style="width: 100%;" class="form-control select2" name="modules[]" multiple="multiple">
                    <?php

                    if(!empty($data['modules'])){
                        foreach ($data['modules'] as $Module){
                            if(!empty($Module['id']) && !empty($Module['exim_info']['version'])){
                                $valModule = $Module['id'].'_v_'.$Module['exim_info']['version'];
                                $valModule2 = $Module['id'].'_v_>'.$Module['exim_info']['version'];

                                $selected = '';
                                $selected2 = '';
                                if(!empty($data['EximConfig']['requireModules'])){
                                    foreach ($data['EximConfig']['requireModules'] as $requireModule){
                                        if($valModule==$requireModule['name_module'].'_v_'.$requireModule['version']){
                                            $selected = 'selected';
                                        }
                                        if($valModule2==$requireModule['name_module'].'_v_'.$requireModule['version']){
                                            $selected2 = 'selected';
                                        }
                                    }
                                }

                                ?><option <?= $selected ?> value="<?= $valModule ?>"><?= $valModule ?></option><?
                                ?><option <?= $selected2 ?> value="<?= $valModule2 ?>"><?= $valModule2 ?></option><?
                            }
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-sm-12">
            <input type="submit" name="submit" class="btn btn-success col-sm-12" value="Сохранить"
                   style="margin-top: 15px;">
        </div>
        <div class="col-sm-12">
            <br>
            <div class="resultTezt"></div>
        </div>
    </form>
    <style>
        .folder-row input {
            width: 90%;
            float: left;
        }

        .folders .folder-row:first-child a {
            display: none;
        }
    </style>
    <style>
        .chooseDirectoryBlock {
            position: absolute;
            background: #ccc;
            padding: 10px;
            z-index: 999999999999999;
        }
    </style>
    <script type="application/javascript">
        $(".select2").select2();
    </script>
</div>