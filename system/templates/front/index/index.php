<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= \core\Html::instance()->title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <?php \core\Html::instance()->setCss('/assets/templates/index/css/bootstrap.min.css'); ?>
    <?php \core\Html::instance()->setCss('/assets/vendors/toastr/dist/jquery.toast.min.css'); ?>
    <?php \core\Html::instance()->setCss('/assets/vendors/font-awesome-4.7.0/css/font-awesome.min.css'); ?>
    <?php \core\Html::instance()->setCss('/assets/templates/index/css/custom.min.css'); ?>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/assets/vendors/html5shiv/dist/html5shiv.js"></script>
    <script src="/assets/vendors/respond/dest/respond.min.js"></script>
    <![endif]-->
    <?php \core\Html::instance()->showCss(true); ?>
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top">

    <div class="navbar-header">
        <a href="/" class="navbar-brand">Тестовый проект</a>
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>
    <div class="navbar-collapse collapse" id="navbar-main">
        <ul class="nav navbar-nav navbar-right">
            <? if (\core\User::current()->isAdmin()) { ?>
                <li><a href="/admin" target="_blank">В административную панель</a></li> <? } ?>
            <? if (\core\User::current()->isAuthorized()) { ?>
                <li><a href="/user/profile" target="_blank">Мой профиль</a></li>
                <li><a href="/user/logout" target="_blank">Выйти</a></li><? } ?>

        </ul>
    </div>

</div>


<div class="clearfix panel-body">
    <div class="wrapper">
        <?= \core\Html::instance()->content; ?>
    </div>
    <? $data = [
        [
            'name'     => 'iphone 11',
            'price'    => 120000,
            'quantity' => 1,
        ],
        [
            'name'  => 'Чехол',
            'price' => 500,
            'quantity' => 1,
        ],
        [
            'name'     => 'Зарядка для iphone 11',
            'price'    => 1500,
            'quantity' => 2,
        ],
    ];
    \modules\tinkoff\widgets\tinkoffButton::instance()->getTinkoffButton($data); ?>
    <div id="source-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Source Code</h4>
                </div>
                <div class="modal-body">
                    <pre></pre>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="row">
            <div class="col-lg-12">

                <p>Made by <a href="http://e-mind.ru">E-mind</a></p>
            </div>
        </div>
    </footer>

</div>
<script src="/assets/vendors/Jquery/jquery-3.2.1.min.js"></script>
<script src="/assets/vendors/jquery-migrate-master/src/jquery-migrate-3.0.0.min.js"></script>
<script src="/assets/vendors/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets/templates/index/js/custom.js"></script>
<script src="/assets/vendors/toastr/dist/jquery.toast.min.js"></script>
<?php \core\Html::instance()->showJs(); ?>
</body>
</html>
