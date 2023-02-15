<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 03.10.2017
 * Time: 13:11
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Восстановление пароля</title>
    <link href="/assets/modules/user/login/css/style.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/assets/vendors/Jquery/jquery-1.11.3.min.js"></script>
</head>
<body>

<div id="wrapper">
    <div class="user-icon forgot-pass-form"></div>

    <form name="login-form" class="login-form" id="form">

        <div class="header">
            <h1>Восстановление пароля</h1>
            <span>Укажите e-mail вашей учетной записи. </span>
        </div>
        <div id="result"></div>
        <div class="content">
            <input name="email" type="text" class="input username" value="<?= ($email) ? $email : ''; ?>"
                   placeholder="E-mail" id="email"/>
            <input type="hidden" name="redirect" id="redirect" value="<?= \core\Request::instance()->redirectUrl; ?>">
        </div>

        <div class="footer">
            <input type="button" name="submit" value="ОТПРАВИТЬ" class="button btn-forgot-pass"/>
            <a class="register" href="/user/registration">Регистрация</a>
            <div class="forgot"><a href="/user/login">вход</a></div>
        </div>

    </form>
</div>
<div class="gradient"></div>
<script src="/assets/vendors/Jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var $document = $(document);
    $document.ready(function () {
        var $username = $('.username');
        $username.focus(function () {
            $(".user-icon").css("left", "-48px");
        });
        $username.blur(function () {
            $(".user-icon").css("left", "0px");
        });

    });
    $document.on('click', '.btn-forgot-pass', function (e) {
        var $form = $('#form').serialize();
        var that = $(this);
        that.attr('disabled', 'true');
        $.ajax({
            url: '/user/forgotpass',
            type: 'POST',
            data: $form,
//            beforeSend: function(){
//                console.log('her');
//
//            },
//            complete: function(){
//                console.log('dd');
//
//            }
        }).done(function (response) {
            var result = JSON.parse(response);
            var container = $('#result');
            container.html(result.msg);

            if (result.success) {
                container.addClass('result-success');

            } else {
                container.addClass('result-error')
            }
            setTimeout(function () {
                that.removeAttr('disabled');
                container.html('');
                container.removeClass('.result-success');
                container.removeClass('.result-error');
            }, 10000);

        })
    })
</script>
</body>
</html>
