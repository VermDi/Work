<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="E-mind Group">
    <meta name="keyword" content="404, error, mistake">
    <link rel="shortcut icon" href="/assets/templates/admin/img/favicon.html">

    <title>Error 404</title>

    <!-- Bootstrap core CSS -->
    <link href="/assets/vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!--external css-->
    <link href="/assets/vendors/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <!-- Custom styles for this template -->
    <link href="/assets/templates/admin/css/style.css" rel="stylesheet">
    <link href="/assets/templates/admin/css/style-responsive.css" rel="stylesheet"/>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="/assets/vendors/bootstrap/js/html5shiv.js"></script>
    <script src="/assets/vendors/bootstrap/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="body-404">
<div class="container">
    <section class="error-wrapper">
        <i class="icon-404"></i>
        <h1>404</h1>
        <h2><?= \core\Html::instance()->content; ?> </h2>
        <?php if (\core\App::$url['path'] != "/") { ?>
            <p class="page-404"><a href="/">На главную...</a></p>
        <?php } else { ?>
            <p class="page-404"><a href="/pages/admin">Хм, а вы страницу создали ?</a></p>
        <?php } ?>
    </section>

</div>
</body>
</html>
