<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Представьтесь пожайлуста</title>
    <link href="/assets/modules/user/login/css/style.css" rel="stylesheet" type="text/css"/>
    <!--    <script type="text/javascript" src="/assets/vendors/Jquery/jquery-1.11.3.min.js"></script>-->
</head>
<body>

<div id="wrapper">
	<?php if (!\modules\user\models\USER::isAuthorized()) { ?>
        <div class="user-email"></div>
        <form name="login-form" class="login-form" action="/user/registrate" method="post">
            <div class="header">
                <h1>Регистрация</h1>
                <span>Укажитие ваши данные.</span>
                <p class="recover-link">Уже есть. Хотите восстановить? <a href="/user/forgotpassword">Перейти</a> на
                    форму восстановления</p>
                <p style="color:red;"><?= (isset($error)) ? $error : ""; ?></p>
            </div>
            <div class="content">
                <label>Ваша почта</label>
                <input name="email" type="text" class="input username"
                       value="<?= (isset($email)) ? $email : "E-mail"; ?>" onfocus=" this.value=''"
                       placeholder="E-mail"/>
                <label>Пароль</label>
                <input name="password" type="password" class="input password" value="" onfocus="this.value=''"
                       placeholder="Пароль"/>
                <label>Пароль еще раз</label>
                <input name="password_repeat" type="password" class="input password password_repeat"
                       value="" onfocus="this.value=''" placeholder="Повтор пароля"/>
                <label>Как к вам обращаться</label>
                <input name="fio" type="text" class="input fio"
                       value="<?= (isset($fio)) ? $fio : "ФИО"; ?>" placeholder="ФИО" onfocus=" this.value=''"/>
                <label>Телефон</label>
                <input name="phone_number" type="tel" class="input phone_number"
                       value="<?= (isset($phone_number)) ? $phone_number : "Номер телефона"; ?>"
                       onfocus=" this.value=''" placeholder="Номер телефона"/>
            </div>
            <div class="footer" style="text-align: center;">
                <input type="submit" name="submit" style="float: none;" value="Регистрация" class="button"/>
            </div>
        </form>
        <div class="gradient"></div>
	<?php } else { ?>
        <h2 class="login-form register" style="text-align: center; color: #678889; padding: 15px;">
            Вы уже авторизованы</h2>
        <br><br><a href="/" class="backbtn">вернуться</a> <?php } ?>
</div>

<script src="/assets/vendors/Jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="/assets/vendors/Inputmask-4.x/dist/inputmask/inputmask.js" type="text/javascript"></script>
<script src="/assets/vendors/Inputmask-4.x/dist/inputmask/inputmask.extensions.js" type="text/javascript"></script>
<script src="/assets/vendors/Inputmask-4.x/dist/inputmask/inputmask.numeric.extensions.js"
        type="text/javascript"></script>
<script src="/assets/vendors/Inputmask-4.x/dist/inputmask/inputmask.phone.extensions.js"
        type="text/javascript"></script>
<script src="/assets/vendors/Inputmask-4.x/dist/inputmask/jquery.inputmask.js" type="text/javascript"></script>
<script src="/assets/vendors/Inputmask-4.x/dist/inputmask/phone-codes/phone.js" type="text/javascript"></script>
<script src="/assets/vendors/Inputmask-4.x/dist/inputmask/phone-codes/phone-ru.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function () {
        let $username = $(".username");
        let $password = $('.password');
        let $fio = $('.fio');
        let $phone_number = $('.phone_number');
        let $password_repeat = $('.password_repeat');
        $phone_number.inputmask({
            'alias': 'phone',
            'removeMaskOnSubmit': true
        });
        $username.focus(function () {
            $(".user-email").css("left", "-48px");
        });
        $username.blur(function () {
            $(".user-email").css("left", "0");
        });

        $password.focus(function () {
            $(".pass-icon").css("left", "-48px");
        });
        $password.blur(function () {
            $(".pass-icon").css("left", "0");
        });

        $password_repeat.focus(function () {
            $(".pass-icon").css("left", "-48");
        });
        $password_repeat.blur(function () {
            $(".pass-icon").css("left", "0");
        });

        $(document).on('blur', '.username', function () {
            let email = $(this).val();
            $.ajax({
                'url': '/user/checkemail',
                type: 'POST',
                data: {
                    'email': email
                },
                'success': function (response) {
                    if (response) {
                        //if(confirm('Уже есть. Хотите восстановить?')){
                        $('.recover-link').find('a').attr('href', $('.recover-link').find('a').attr('href') + "/" + email);
                        $('.recover-link').show();
                        //location.href='/user/forgotpassword'
                        // }
                    }
                    //console.log(response);
                }
            })
        });
        $(document).on('blur', '.phone_number', function () {
            let phone_number = $(this).val();
            $.ajax({
                'url': '/user/checkphone',
                type: 'POST',
                data: {
                    'phone': phone_number
                },
                'success': function (response) {
                    if (response) {
                        // if (confirm('Уже есть. Хотите восстановить?')) {
                        //
                        $('.recover-link').show();
                        //location.href='/user/forgotpassword'
                        //  }
                        //  }
                        //console.log(response);
                    }
                }
            })
        });

    });
</script>
</body>
</html>