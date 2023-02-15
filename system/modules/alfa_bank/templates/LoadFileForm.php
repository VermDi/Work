<div class="panel">
    <div class="panel-heading">
        <div class="pull-left"> Выберите файл для загрузки (пример можно скачать по кнопка "выгрузить")</div>
        <div class="pull-right btn-group"><a href="/alfa_bank/admin" class="btn btn-xs btn-success"><< к списку</a>
            <a href="/alfa_bank/admin/tocsv" class="btn btn-xs btn-primary">Скачать CSV</a><a
                href="/alfa_bank/admin/fromcsv" class="btn btn-xs btn-default">Загрузить CSV</a></div>
    </div>
    <div class="panel-body">
        <form action="/alfa_bank/admin/fromcsv" method="post" enctype="multipart/form-data">
            <p><input type="file" name="CSV"></p>
            <p>
                <button type="submit" class="btn btn-sm btn-primary">Загрузить CSV файл</button>
            </p>
        </form>
        <blockquote>
            <p>Обратите внимание, что если в файле указан ID то произойдет обновление записи, если не указан, то
                вставка.</p>
            <p> Все ошибки отобразятся на экране.</p>
        </blockquote>
    </div>
</div>

