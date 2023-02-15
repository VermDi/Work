<div class="col-sm-12">
    <p>Файл конфигурации /core/version.php</p>
    <form action="#" method="post" enctype="multipart/form-data" class="CoreUploadForm">
        <div class="col-sm-12">
            <div>
                <label for="name" class="smalllabel">Версия</label>
                <input value="<?= (isset($data['EximConfig']['version']))?$data['EximConfig']['version']:'' ?>" type="text" name="version" class="form-control input-sm" required>
            </div>
        </div>
        <div class="col-sm-12">
            <div>
                <label for="name" class="smalllabel">Описание версии</label>
                <input value="<?= (isset($data['EximConfig']['ModuleInfo']['version_description']))?$data['EximConfig']['ModuleInfo']['version_description']:'' ?>" type="text" name="version_description" class="form-control input-sm" required>
            </div>
        </div>
        <div class="col-sm-12">
            <div>
                <label for="name" class="smalllabel">Список папок и файлов, каждый с новой строки<br>
                    пример:<br>
                    system/core<br>
                    system/src<br>
                    system/templates/system/blank/blank.php<br>
                </label>
                <textarea rows="15" class="form-control input-sm" name="Folders"><?php
                    if(isset($data['EximConfig']['Folders'])){
                        if(is_array($data['EximConfig']['Folders'])){
                            foreach ($data['EximConfig']['Folders'] as $Folder){
                                echo $Folder.PHP_EOL;
                            }
                        }
                    }
                    ?></textarea>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                <input type="submit" data-action="1" name="submit" class="btn btn-success col-sm-12" value="Сохранить и проверить настройки" style="margin-top: 15px;">
            </div>
            <div class="col-sm-6">
                <a data-id="core" class="btn btn-info col-sm-12 uploadServerCore" href="/exim/upload/core" style="margin-top: 15px; display: none;">Загрузить на сервер</a>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="resultTezt"></div>
        </div>
    </form>
</div>