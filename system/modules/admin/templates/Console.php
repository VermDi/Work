<div class="panel col-sm-12">
    <div class="panel-heading">Консоль! Используйте на ваш страх и риск, если есть сомнения удалить файл консоли!!!
    </div>
    <div class="panel-body">
        <div class="col-sm-12">
            <textarea id="command" name="message" class="form-control"></textarea>
            <button type="button" class="btn btn-success btn-sm" style="width: 100%;" onclick="sendConsoleCommand();">
                Выполнить
            </button>
        </div>
        <div class="col-sm-12" id="console-result">

        </div>
    </div>
</div>
<script>
    function sendConsoleCommand() {
        $.ajax({
            method: "POST",
            url: "/admin/console",
            data: {message: $("#command").val()}
        })
            .done(function (msg) {
                $("#console-result").html(msg);
            });
    }

</script>