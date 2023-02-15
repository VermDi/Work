<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 04.10.2017
 * Time: 14:48
 */
/**
 * @var $data array
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
    <div class="pass-icon forgot-pass-form"></div>

    <form name="login-form" class="login-form" id="form">

        <div class="header">
            <h1>Восстановление пароля</h1>
            <span>Введите новый пароль. </span>
        </div>
        <div id="result"></div>
        <div class="content">
            <input name="password" type="password" class="input password" value="Пароль" onfocus="this.value=''"/>
            <input name="password_repeat" type="password" class="input password password_repeat"
                   value="Повторите пароль" onfocus="this.value=''"/>
            <input type="hidden" name="token" value="<?= $data['token']; ?>"/>
            <input type="hidden" name="redirect" value="<?= \core\Request::instance()->redirectUrl; ?>">
        </div>

        <div class="footer">
            <input type="button" name="submit" value="ОТПРАВИТЬ" class="button btn-recover-pass"/>
            <a class="register" href="/user/registration">Регистрация</a>
            <div class="forgot"><br><br><a href="/user/login">войти</a></div>
        </div>

    </form>
</div>
<div class="gradient"></div>
<script src="/assets/vendors/Jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var $document = $(document);
    $document.ready(function () {
        var $password = $('.password');
        var $password_repeat = $('.password_repeat');

        $password.focus(function () {
            $(".pass-icon").css("left", "-48px");
        });
        $password.blur(function () {
            $(".pass-icon").css("left", "0px");
        });

        $password_repeat.focus(function () {
            $(".pass-icon").css("left", "-48px");
        });
        $password_repeat.blur(function () {
            $(".pass-icon").css("left", "0px");
        });
    });
    $document.on('click', '.btn-recover-pass', function (e) {
        var $form = $('#form').serialize();
        var that=$(this);
        $.ajax({
            url: '/user/resetpassword',
            type: 'POST',
            data: $form,
            beforesend: function(){
                that.attr('disabled','true');
            },
            complete: function(){
                that.attr('disabled','false');
            }
        }).done(function (response) {
            var result = JSON.parse(response);
            var container = $('#result');
            container.html(result.msg);
            if (result.success) {
                container.addClass('result-success');
                setTimeout(function () {
                    location.href = '/user/login'
                }, 5000);

            } else {
                container.addClass('result-error')
            }


        })
    })
</script>
</body>
</html>
