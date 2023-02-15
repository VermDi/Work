<?php namespace core;
/**
 * Базовый класс для отображения ошибок
 * Class Errors
 *
 * @package core
 */
class Errors
{

	private static $instance;

	/**
	 * Errors constructor.
	 */
	public function __construct()
	{
		self::$instance = self::$instance ?: $this;
	}

	/**
	 * Выбрасывает страницу 404 в соотвествующем дизайне.
	 *
	 * @param bool $err
	 */
	static function e404($err = false)
	{
		header("HTTP/1.0 404 Not Found");
		if ($err == false) {
			Html::instance()->content = "Страница не найдена.";
		} else {
			Html::instance()->content = $err;
		}
		Html::instance()->renderTemplate("@404")
			->show();
		die();
	}

	/**
	 * Выбрасывает страницу и код 500.
	 *
	 * @param bool $err
	 * @param $e \Exception
	 */
	static function e500($err = false, $e = false)
	{

		if ($e) {
			ob_start();
			?>
			<div class="pull-left" style="text-align: left; padding: 25px;">
				<b>Возникла ошибка:</b> <span class="red-text"> <?= $e->getMessage(); ?></span>
				<?php if (strpos($e->getMessage(), 'Base table or view not found')) {
					echo "<hr>Возможно поможет миграция <a href='/migrations/up' class='btn btn-primary'>Запустить</a><hr> ";
				} ?>

				<br>
				<?php $fileWithError = $e->getFile();
				$lineWithError = $e->getLine(); ?>
				<?php if ($e instanceof \PDOException) {
					$fileWithError = $e->getTrace()[1]['file'];
					$lineWithError = $e->getTrace()[1]['line'];
				} ?>

				<b>В файле:</b> <?= $fileWithError; ?><br>
				<b>Строка:</b> <?= $e->getLine(); ?><br>
				<b>Код ошибки:</b> <?= $e->getCode(); ?><br>
				<code><?php $line_start = $lineWithError - 5;
					$line_end = $lineWithError + 5;
					$line = 0;
					$fp = fopen($fileWithError, 'r');

					while (($buffer = fgets($fp)) !== false) {
						$line++;
						if ($line < $line_start) {
							continue;
						}
						if ($line > $line_end) {
							break;
						}
						?>
						<div class="code_row <?= ($line == $lineWithError) ? "active" : "" ?>"><span
								class="number_line"><?= $line; ?></span><span
								class="line_text"><?= $buffer; ?></span></div>
						<?
					}
					fclose($fp); ?></code>

				<?
				?>
				<b>Трасировка пути:</b>
				<hr>
				<table class="table table-bordered table-hovered" style="font-size: 11px;">
					<tr>
						<td>#</td>
						<td>Файл</td>
						<td>Строка</td>
						<td>Функция</td>
						<td>Класс</td>
					</tr>

					<?php foreach ($e->getTrace() as $k => $v) {
						?>
						<tr<?= ($e instanceof \PDOException and ($k == 1 or $k == 2)) ? " class='active-pdo'" : ((!$e instanceof \PDOException and $k == 0) ? " class='active-pdo'" : ""); ?>>
							<td><?= $k; ?></td>
							<td>><?= isset($v['file']) ? $v['file'] : ""; ?>
								<?php if (!empty($v['args'][0]) and is_string($v['args'][0]) and $k == 0) {
									echo "<br><b>" . $v['args'][0] . "</b>";
								} ?></td>
							<td><?= isset($v['line']) ? $v['line'] : ""; ?></td>
							<td><?= isset($v['function']) ? $v['function'] : ""; ?></td>
							<td><?= isset($v['class']) ? $v['class'] : ""; ?></td>
						</tr>
						<?
					} ?></table>

			</div>
			<?
			Html::instance()->content = ob_get_contents();
			ob_end_clean();
		}
		/*
		 * TODO: РАзобраться как эта херня попадает на страницу при загрузке!
		 */
		//header('500 Internal Server Error', true, 500);
		if ($err == false) {
			if (empty($e)) {
				Html::instance()->content = "Страница не найдена.";
			}
		} else {
			Html::instance()->content = $err;
		}
		if (!_DEVELOPER_MODE_) {
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/errors.txt", Html::instance()->content, FILE_APPEND);
			Html::instance()->content = "Возникла ошибка! Сообщите администратору сайта!";
		}
		Html::instance()->renderTemplate("@500")
			->show();
		die();
	}

	/**
	 * Выбрасывает страницу с критичной ошибкой.
	 *
	 * @param bool $err
	 */
	static function criticalEror($err = false)
	{

		if (_DEVELOPER_MODE_) {
			ob_start();
			echo($err);
			echo "<pre>";
			debug_print_backtrace();
			echo "</pre>";
			Html::instance()->content = ob_get_contents();
			ob_end_clean();
			Html::instance()->renderTemplate("@500")
				->show();
		} else {
			self::e404();
		}
	}

	/**
	 * Выкидывает ошибку в json
	 *
	 * @param $error
	 */
	public static function ajaxError($error)
	{
		Response::ajaxError($error);
	}

	/**
	 * выкидывает json с успешной обработкой и ошибкой = 0
	 *
	 * @param $message
	 */
	public static function ajaxSuccess($message)
	{
		Response::ajaxSuccess($message);
	}
}
