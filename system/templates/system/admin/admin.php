<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= \core\Html::instance()->title; ?></title><?= \core\Html::instance()->meta; ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap core CSS -->
    <?php \core\Html::instance()->setCss("/assets/vendors/bootstrap/css/bootstrap.min.css"); ?>
    <?php \core\Html::instance()->setCss("/assets/vendors/bootstrap/css/bootstrap-reset.css"); ?>
    <?php \core\Html::instance()->setCss("/assets/vendors/font-awesome-4.7.0/css/font-awesome.css"); ?>
    <?php \core\Html::instance()->setCss("/assets/templates/admin/css/slidebars.css"); ?>
    <?php \core\Html::instance()->setCss("/assets/templates/admin/css/style.css"); ?>
    <?php \core\Html::instance()->setCss("/assets/templates/admin/css/style-responsive.css"); ?>
    <?php \core\Html::instance()->setCss("/assets/vendors/select2/css/select2.css"); ?>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="/assets/templates/admin/js/html5shiv.js"></script>
    <script src="/assets/templates/admin/js/respond.min.js"></script>
    <![endif]-->
    <!--  c true экономия порядка 40 ms но надо победить картинки -->
    <?php \core\Html::instance()->showCss(true); ?>
</head>

<body>

<section id="container" class="">
    <!--header start-->
    <header class="header white-bg">
        <div class="sidebar-toggle-box">
            <div data-original-title="Toggle Navigation" data-placement="right" class="fa fa-bars tooltips"></div>
        </div>
        <!--logo start-->
        <a href="/admin" class="logo">Mind<span>CMS</span></a>
        <!--logo end-->

        <div class="top-nav ">
            <ul class="nav pull-right top-menu">
                <li>
                    <input type="text" class="form-control search" placeholder="Search">
                </li>
                <!-- user login dropdown start-->
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <!-- <img alt="" src="img/avatar1_small.jpg"> -->
                        <span class="username"><?= \modules\user\models\USER::getLogin(); ?></span>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu extended logout">
                        <div class="log-arrow-up"></div>
                        <li><a href="/user/profile"><i class=" fa fa-suitcase"></i>Профиль</a></li>
                        <li><a href="/user/logout"><i class="fa fa-key"></i> Выйти</a></li>
                    </ul>
                </li>

                <!-- user login dropdown end -->
                <li class="sb-toggle-right">
                    <i class="fa  fa-align-right"></i>
                </li>
            </ul>
        </div>
    </header>
    <!--header end-->
    <!--sidebar start-->
    <aside class="clearfix">
        <div id="sidebar" class="nav-collapse clearfix">
            <?
            	\modules\menu\widgets\Show::Menu(1);
            ?>
        </div>
    </aside>
    <!--sidebar end-->
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper site-min-height">
            <!-- page start-->
            <?= \core\Html::instance()->content; ?>
            <!-- page end-->
        </section>
    </section>
    <!--main content end-->

    <!-- Right Slidebar start -->
    <div class="sb-slidebar sb-right sb-style-overlay">
        СКОРО

    </div>
</section>

<!-- js placed at the end of the document so the pages load faster -->
<!--<script src="/assets/templates/admin/js/jquery.js"></script>-->
<?php \core\Html::instance()->toTop()->setJs('/assets/vendors/Jquery/jquery-3.2.1.min.js')
    ->setJs('/assets/vendors/jquery-migrate-master/src/jquery-migrate-3.0.0.min.js')
    ->setJs('/assets/templates/admin/js/bootstrap.min.js')
    ->setJs('/assets/templates/admin/js/jquery.scrollTo.min.js')
    ->setJs('/assets/templates/admin/js/slidebars.min.js')
    ->setJs('/assets/templates/admin/js/jquery.nicescroll.js')
    ->setJs('/assets/templates/admin/js/respond.min.js')
    ->setJs('/assets/templates/admin/js/common-scripts.js')
    ->setJs('/assets/vendors/select2/js/select2.js')
    ->setJs('/assets/vendors/select2/js/i18n/ru.js')->stopToTop(); ?>
<?php \core\Html::instance()->showJs(false, false); ?>
<?php \modules\debuger\widgets\DebugPanel::instance()->show(); ?>
</body>

</html>
