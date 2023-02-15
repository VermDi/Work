<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 13.09.2017
 * Time: 15:52
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= \core\Html::instance()->title; ?></title><?= \core\Html::instance()->meta; ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <!-- Bootstrap core CSS -->
    <link href="/assets/vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/vendors/bootstrap/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="/assets/vendors/font-awesome-4.7.0/css/font-awesome.css" rel="stylesheet"/>

    <!--right slidebar-->
    <link href="/assets/templates/admin/css/slidebars.css" rel="stylesheet">
    <link href="/assets/vendors/slick-1.6.0/slick/slick.css" rel="stylesheet">
    <link href="/assets/vendors/slick-1.6.0/slick/slick-theme.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/assets/templates/admin/css/style.css" rel="stylesheet">
    <link href="/assets/templates/blank/css/style.css" rel="stylesheet">
    <link href="/assets/templates/admin/css/style-responsive.css" rel="stylesheet"/>
    <link rel="stylesheet" href="/assets/vendors/select2/css/select2.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="/assets/templates/admin/js/html5shiv.js"></script>
    <script src="/assets/templates/admin/js/respond.min.js"></script>
    <![endif]-->
    <?php html()->css(); ?>
</head>

<body style="min-height: 100vh;">
<section id="container" class="blank-container" style="display: flex; min-height: 100vh; flex-direction: column;">
    <!--main content start-->
    <section id="main-content" class="main-blank-content" style="flex-grow: 1;">
        <section class="wrapper">
            <?php //\modules\slider\widgets\Slider::show(1);?>
            <!-- page start-->
            <?= \core\Html::instance()->content; ?>
            <!-- page end-->
        </section>
    </section>
    <!--main content end-->

    <!--footer start-->
    <footer class="site-footer blank-footer">
        <div class="text-center">
            2014 - <?= date('Y'); ?> &copy; <a href="http://www.e-mind.ru" style="color: #fff;">E-Mind studio</a>
            <a href="#" class="go-top">
                <i class="fa fa-angle-up"></i>
            </a>
        </div>
    </footer>
    <!--footer end-->
</section>

<!-- js placed at the end of the document so the pages load faster -->
<!--<script src="/assets/templates/admin/js/jquery.js"></script>-->
<script src="/assets/vendors/Jquery/jquery-3.2.1.min.js"></script>
<script src="/assets/vendors/jquery-migrate-master/src/jquery-migrate-3.0.0.min.js"></script>
<script src="/assets/templates/admin/js/bootstrap.min.js"></script>
<script src="/assets/templates/admin/js/jquery.scrollTo.min.js"></script>
<script src="/assets/templates/admin/js/slidebars.min.js"></script>
<script src="/assets/templates/admin/js/jquery.nicescroll.js" type="text/javascript"></script>

<script src="/assets/templates/admin/js/respond.min.js"></script>
<!--common script for all pages-->
<script src="/assets/templates/admin/js/common-scripts.js"></script>
<script src="/assets/vendors/select2/js/select2.js"></script>
<?
// i18n/ru.js нужен для select2
?>
<script src="/assets/vendors/select2/js/i18n/ru.js"></script>
<?php html()->js(); ?>

</body>

</html>
