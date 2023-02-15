<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Представьтесь пожайлуста</title>
    <link href="/assets/modules/user/login/css/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/assets/vendors/Jquery/jquery-1.11.3.min.js"></script>
</head>
<body>

<div id="wrapper">
    <div class="user-icon"></div>
    <div class="pass-icon"></div>

    <form name="login-form" class="login-form" action="/users/token" method="post">

        <div class="header">
            <h1>Подтвердите EMAIL</h1>
            <span>Пройдите по ссылке из письма, либо подтвердите ваш аккаунт, указав код из письма. </span>
        </div>

        <div class="content">
            <input name="token" type="text" class="input username" value="Код из письма" onfocus="this.value=''" />
        </div>

        <div class="footer">
            <input type="submit" name="submit" value="Подтвердить" class="button" />
        </div>

    </form>
</div>
<div class="gradient"></div>

<script type="text/javascript">
    $(document).ready(function() {
        $(".username").focus(function() {
            $(".user-icon").css("left","-48px");
        });
        $(".username").blur(function() {
            $(".user-icon").css("left","0px");
        });
    });
</script>
</body>
</html>