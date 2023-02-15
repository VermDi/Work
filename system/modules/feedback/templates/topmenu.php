<?php if (isset($_SESSION['answer'])) { ?>
    <div class="<?= $_SESSION['answer']['type'] ?> session-answer"><div class="alert alert-success" role="alert"><h4><?=
                $_SESSION['answer']['text'];
                unset($_SESSION['answer']);
                ?></h4></div></div> 
<?php } ?>
<div class="col-sm-12">
    <div class="highlight">
        <span><a href="/feedback/admin">Feedback</a></span>
    </div>

    <div class="control-links">
        <a href="/feedback/admin" class="btn btn-primary<?=($action == 'index') ? ' active' : '' ?>">
            Управление обращениями
        </a>
        <a href="/feedback/admin/addform" class="btn btn-primary<?=($action == 'addform') ? ' active' : '' ?>">
            Добавить новую форму обращения
        </a>
        <a href="/feedback/admin/listforms" class="btn btn-primary<?=($action == 'listforms') ? ' active' : '' ?>">
            Список форм обращения
        </a>
    </div>

</div>

