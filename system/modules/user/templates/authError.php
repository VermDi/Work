<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 07.11.2017
 * Time: 15:41
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ошибка авторизации</title>
    <link href="/assets/modules/user/login/css/style.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/assets/vendors/Jquery/jquery-1.11.3.min.js"></script>
</head>
<body>

<div id="wrapper">
    <div class="user-icon"></div>
    <div class="pass-icon"></div>
    <div class="login-form">
        <div class="header">
            <h1>Авторизация</h1>
            <span>Ошибка авторизации. </span>
        </div>

        <div class="content result-error">
            Невозможно авторизоваться. Возможно аккаунт заблокирован.
        </div>

        <div class="footer">
            <a class="register" href="/user/registration">Регистрация</a>
            <div class="forgot" style="padding: 0"><a href="/user/login">войти</a></div>
        </div>

    </div>
</div>
<div class="gradient"></div>
</body>
</html>
