<div class="col-sm-12">
    <form action="/exim/savemodule" method="post" enctype="multipart/form-data" class="moduleForm">
        <div class="col-sm-12">
            <div>
                <label for="name" class="smalllabel">Название модуля</label>
                <input type="text" name="name" class="form-control input-sm" required>
            </div>
        </div>
        <div class="col-sm-12">
            <input type="submit" data-action="1" name="submit" class="btn btn-success col-sm-12" value="Создать модуль" style="margin-top: 15px;">
        </div>
        <div class="col-sm-12">
            <div class="resultTezt"></div>
        </div>
    </form>
</div>