<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Представьтесь пожайлуста</title>
	<link href="/assets/modules/user/login/css/style.css" rel="stylesheet" type="text/css"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="/assets/vendors/Jquery/jquery-1.11.3.min.js"></script>
	<link rel="stylesheet" href="/assets/vendors/toastr/dist/jquery.toast.min.css">
</head>
<body>

<div id="wrapper">
	<div class="user-icon"></div>
	<div class="pass-icon"></div>

	<form name="login-form" class="login-form" action="" method="post">

		<div class="header">
			<h1>Авторизация</h1>
			<span>Введите ваши регистрационные данные для входа в ваш личный кабинет. </span>
		</div>

		<div class="content">
			<input name="email" type="text" class="input username" value="" placeholder="E-mail"/>
			<input name="password" type="password" class="input password" placeholder="Пароль"/>
			<input type="hidden" name="redirect" value="<?= \core\Request::instance()->redirectUrl; ?>">
		</div>

		<div class="footer">
			<input type="submit" name="submit" value="ВОЙТИ" class="button" id="send-login-btn"/>
			<a class="register" href="/user/registration">Регистрация</a>
			<div class="forgot"><br><br><a href="/user/forgotpassword">Забыли пароль?</a></div>
		</div>

	</form>
</div>
<div class="gradient"></div>
<script src="/assets/vendors/Jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="/assets/vendors/toastr/dist/jquery.toast.min.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$('input').focus(function () {
			$(this).attr('placeholder', '');
		});
		var $username = $('.username');
		var $password = $('.password');
		$username.focus(function () {
			$(".user-icon").css("left", "-48px");
		});
		$username.blur(function () {
			$(".user-icon").css("left", "0px");
		});

		$password.focus(function () {
			$(".pass-icon").css("left", "-48px");
		});
		$password.blur(function () {
			$(".pass-icon").css("left", "0px");
		});
		$(document).on('submit', 'form[name="login-form"]', function (e) {
			e.preventDefault();
			var form = $(this).serialize();
			if (($username.val() !== "") && ($password.val() !== "")) {
				document.getElementById('send-login-btn').style.visibility = "hidden";
				$.ajax({
					url: '/user/login/ajax',
					'type': 'POST',
					'data': form,
					'dataType': 'json'

				}).done(function (response) {

					if (response.status === "OK" && response.redirect) {
						if (response.states) {
							localStorage.setItem('w2ui', JSON.stringify(response.states))
						}
						location.href = response.redirect;
					} else if (response.status === "OK") {
						$.toast({
							'text': "Успешная авторизация",
							'position': 'top-right',
							'loader': false,
							'icon': 'success',
							'bgColor': 'green',
							'hideAfter': 5000
						})
					} else if (response.status === 'ERROR') {
						$.toast({
							'text': response.message,
							'position': 'top-right',
							'loader': false,
							'icon': 'warning',
							'bgColor': '#c85c57',
							'hideAfter': 5000
						})
					} else {
						$.toast({
							'text': "Неизвестная ошибка",
							'position': 'top-right',
							'loader': false,
							'icon': 'warning',
							'bgColor': '#c85c57',
							'hideAfter': 5000
						})
					}
					document.getElementById('send-login-btn').style.visibility = "visible";
				})
			}
		})
	});
</script>
</body>
</html>