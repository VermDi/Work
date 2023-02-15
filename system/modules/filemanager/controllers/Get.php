<?php

class file_manager
{
	public $act;
	public $path;
	public $file_w;
	public $full_file_tree;
	function __construct()
	{
		if (!empty($_POST))
		{
			if (!empty($_POST['act']))
			{
				$this->act = $_POST['act'];
			}
			if (!empty($_POST['path']))
			{
				$this->path = $_POST['path'];
			}
			if (!empty($_POST['file']))
			{
				$this->file_w = $_POST['file'];
			}
			if (!empty($_POST['folder']))
			{
				$this->folder = $_POST['folder'];
			}
		}
		else
		{
			if (!empty($_GET['act']))
			{
				$this->act = $_GET['act'];
			}
			if (!empty($_GET['path']))
			{
				$this->path = $_GET['path'];
			}
			if (!empty($_GET['file']))
			{
				$this->file_w = $_GET['file'];
			}
			if (!empty($_GET['folder']))
			{
				$this->folder = $_GET['folder'];
			}
		}

		switch ($this->act)
		{
			case "get":
				$this->get_list();
			break;
			case "edit_file":
				$this->open_file_to_edit();
			break;
			case "save_file":
				$this->save_in_file();
			break;
			case "del":
				$this->del();
			break;
			case "download":
				$this->file_download();
			break;
			case "zip":
				$this->get_zip();
			break;
			case "file_upload":
				$this->file_upload();
			break;
			case "mk_dir":
				$this->mk_dir();
			break;
			case "mk_file":
				$this->mk_file();
			break;
			case "clear_folder":
				$this->clear_folder();
			break;
			case "unzip":
				$this->unzip();
			break;
		}
	}
	/////////////////////////////
	// UNZIP
	///////////////////////////////
	function unzip()
	{
//		die($this->file_w);
		$zip = new ZipArchive; //новый класс
		if ($zip->open($this->file_w) === TRUE)
		{
			$zip->extractTo(substr($this->file_w, 0, strrpos($this->file_w, '/'))."/");
			$zip->close();
			echo "ok";
		}
		else
		{
			echo "ошибка";
		}
	}
	///////////////////////////
	// Чистим папку
	///////////////////////////
	function clear_folder()
	{
		$fs=\core\FS::instance();
		$fs->removeFolder($this->folder, false);

		header ("Location: ".$_SERVER["HTTP_REFERER"]."#".$this->folder);
	}
	///////////////////////////////
	// создание файла
	///////////////////////////////
	function mk_file()
	{
//		die($this->path.$_POST['dir_name']));
		$fn = $this->path.DIRECTORY_SEPARATOR.strtolower($_POST["file_name"]);
		$fop=fopen($fn, "a+");
		chmod($fn, _FILE_R_);
		fclose($fop);
		header ("Location: ".$_SERVER["HTTP_REFERER"]."#".$this->path);
	}
	////////////////////////////////
	// создание папки
	//////////////////////////////////
	function mk_dir()
	{
//		die($this->path.$_POST['dir_name']));
		mkdir($this->path.DIRECTORY_SEPARATOR.strtolower($_POST["dir_name"]), _FOLDER_R_);
		header ("Location: ".$_SERVER["HTTP_REFERER"]."#".$this->path);
	}
	/////////////////////////////////
	// Загрузка файла на сервер!
	//////////////////////////////
	function file_upload()
	{
		foreach ($_FILES['file']['tmp_name'] as $k=>$v)
		{
//			echo $v."--".$this->path."/".$_FILES['file']['name'][$k]."<br>";
			if (!move_uploaded_file($v, $this->path.DIRECTORY_SEPARATOR.$_FILES['file']['name'][$k]))
			{
				echo "Ошибка перемещения файла: ".$this->path.DIRECTORY_SEPARATOR.$_FILES['file']['name'][$k];
			}
		}
		header("Location: ".$_SERVER["HTTP_REFERER"]);
	}
	////////////////////////////////////
	// Вспомогательная функция прохода по папкам
	/////////////////////////////////////
	function open_folder($folder=false)
	{
//		echo $folder;
		if (!$path=scandir($folder))
		{
			die("Ошибка сканирования директории: ".$folder);
		}
		if ($path != false)
		{
			foreach ($path as $k=>$v)
			{
				if ($v[0] != '.')
				{
					if (!is_file($folder.DIRECTORY_SEPARATOR.$v))
					{
						$this->open_folder($folder.DIRECTORY_SEPARATOR.$v);
					}
					else
					{
						$this->full_file_tree[] = $folder.DIRECTORY_SEPARATOR.$v;
					}
				}
			}
		}
	}
	///////////////////////////////////////
	// Функция скачивания архива! В виде ЗИПА
	////////////////////////////////////////
	function get_zip()
	{
		$zip = new ZipArchive; //новый класс
		if (!file_exists(_ROOT_PATH_.DIRECTORY_SEPARATOR."temp"))
		{
			mkdir(_ROOT_PATH_.DIRECTORY_SEPARATOR."temp", _FOLDER_R_);
		}
		else
		{
			chmod(_ROOT_PATH_.DIRECTORY_SEPARATOR."temp",_FOLDER_R_);
		}
		$res = $zip->open(_ROOT_PATH_.DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR.md5($this->folder).".zip", ZIPARCHIVE::CREATE);
		if ($res === true)
		{
			$this->open_folder($this->folder);
			if (is_array($this->full_file_tree)) {
                reset($this->full_file_tree);
                foreach ($this->full_file_tree as $k => $v) {
//				$zip->addFile($file, iconv("CP1251", "866", $file));
//				$zip->addFile($v, iconv("UTF-8", "866", substr($v, strlen(_ROOT_PATH_))));
                    $zip->addFile($v, substr($v, strlen(_ROOT_PATH_ . DIRECTORY_SEPARATOR)));
                }
            }
			//closedir($dir);
			$zip->close();
//			echo "<p>Файлы добавлены в архив</p>";
		}
		else
		{
			echo "<p>Ошибка</p>";
		}

		$this->file_download(_ROOT_PATH_.DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR.md5($this->folder).".zip");
        die('END 214 line');

	}
	////////////////////////////
	////////////////////////////////////////
	// функция скачивания файла
	//////////////////////////////////////////
    function file_download($filename=false, $mimetype="application/octet-stream")
    {
        if (empty($filename))
        {
            $filename = $this->file_w;
        }
        if (file_exists($filename))
        {
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Description: File Transfer');
            header('Content-Type: '.$mimetype);
            header('Content-Disposition: attachment; filename=' . basename($filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            // Открываем искомый файл
            $f = fopen($filename, 'r');
            while (!feof($f))
            { // Читаем килобайтный блок, отдаем его в вывод и сбрасываем в буфер
                echo fread($f, 1024);
                flush();
            }
            // Закрываем файл
            fclose($f);
            die();
        }
        else
        {
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            header("Status: 404 Not Found");
        }
        exit;
    }
	////////////////////////
	// Функция хождения по каталогу  и вывода инофрмации на экран юзера
	///////////////////////////
	function get_list()
	{
		$txt_war = "";
		if (is_readable($this->path))
		{
			if (!$path=scandir($this->path))
			{
		 		$txt_war = "Ошибка получения директории!";
			}
		}
		else
		{
			$this->path = $_SERVER["DOCUMENT_ROOT"]; // переопределяем на уровнь выше!!!!
		}
		if (!$path=scandir($this->path))
		{
			$txt_war .= "<br>Получить корневую директорию тоже не удалось!";
		}
		if ($txt_war != "")
		{
?>
<i class="icon-warning-sign"></i> <?=$txt_war;?>
<?
		}
?>
<p><a href="#" onclick="show_hide('file_upload');return false;" class="btn btn-sm btn-primary"><i class="fa fa-upload"></i> Загрузить</a> <a href="#" onclick="show_hide('mk_dir');return false;" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Создать папку</a> <a href="#" onclick="show_hide('mk_file');return false;" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Создать файл</a> <a href="/filemanager/get?act=clear_folder&folder=<?=$this->path;?>" class="btn btn-sm btn-primary" onclick="return confirm('Уверены!? Все в текущей папке будет удалено!');"><i class="fa fa-trash-o"></i> Почистить папку</a></p>
<div id="file_upload" style="display:none;" class="img-polaroid">
	<form id="form2" name="form2" enctype="multipart/form-data" method="post" action="/filemanager/get">
	<label for="fileField"></label>
	<input type="file" name="file[]" id="fileField" required multiple />
	<label><input type="hidden" name="act" value="file_upload" class="btn" /></label>
	<label><input type="hidden" name="path" value="<?=$this->path;?>" /></label>
	<label><input name="sub" type="submit" id="sub" value="Отправить" class="btn" /></label>
	</form>
</div>
<div id="mk_dir" style="display:none;" class="img-polaroid">
	<form id="form3" name="form3" enctype="multipart/form-data" method="post" action="/filemanager/get">
	Название папки:
	<label><input type="text" name="dir_name" value="" /></label>
	<label><input type="hidden" name="act" value="mk_dir" class="btn" /></label>
	<label><input type="hidden" name="path" value="<?=$this->path;?>" /></label>
	<label><input name="sub" type="submit" id="sub" value="Создать" class="btn" /></label>
	</form>
</div>
<div id="mk_file" style="display:none;" class="img-polaroid">
	<form id="form4" name="form4" enctype="multipart/form-data" method="post" action="/filemanager/get">
	Название файла:
	<label><input type="text" name="file_name" value="" /></label>
	<label><input type="hidden" name="act" value="mk_file" class="btn" /></label>
	<label><input type="hidden" name="path" value="<?=$this->path;?>" /></label>
	<label><input name="sub" type="submit" id="sub" value="Создать" class="btn" /></label>
	</form>
</div>
<?
		global $start_path;
		$full_way = "";
		$real_way = "";
		$way_in_massive = explode('/', $this->path);
		foreach ($way_in_massive as $k=>$v)
		{
			$real_way .= $v."/";
			if (strlen($start_path) >= strlen($real_way))
			{ // gпроверяем что собрался путь до обозначенного корня
				//$full_way="<i class=\"icon-home\"></i>";
			}
			else
			{ // если собрался выодить хлебный крошки
				$wway = substr($real_way, 0, strrpos(substr($real_way, 0, -1), '/'));
				if (strlen($start_path) > strlen($wway))
				{
					$wway = substr($start_path, 0, strrpos(substr($start_path, 0, -1), '/'));
				}
				$full_way .= "<li><a href='#".$wway."/".$v."' class=\"\">".$v."</a></li>";
			}
		}
?>
<div class="col-md-12"><ul class="breadcrumb"><?=$full_way;?></ul></div>
<ul class="">
<?
		//echo $this->path;
		//echo $_SERVER['DOCUMENT_ROOT'];
		//echo "------>";print_r($path);
		//echo $this->path."--".$start_path;
		if ($start_path!=$this->path)
		{
?>
<li style="clear: both;"><a href="#<?=substr($this->path, 0, strrpos($this->path, '/'));?>"><i class="fa fa-level-up"></i></a>
<?
		}
		foreach ($path as $k=>$v)
		{
			if ($v[0] != '.')
			{
?>
<li style="clear: both; padding: 4px;">
    <span style="float:left; margin-right:25px; width:250px;  white-space: nowrap; height:25px; overflow:hidden;">
<?
				if (!is_file($this->path."/".$v))
				{
?>
      <i class="fa fa-folder-open"></i>&nbsp;<a href="#<?=$this->path."/".$v;?>"><?=$v;?></a></span>
<?
				}
				else
				{
?>
    <i class="fa fa-file"></i>&nbsp;<?=$v;?></span>
    <?=number_format(filesize($this->path."/".$v)/1024, 3, '.', ',').' Кб'?>&nbsp;
    <a href="/filemanager/get?act=download&file=<?=$this->path."/".$v;?>" class="btn btn-xs btn-success"><i class="fa fa-download"></i>Скачать</a>
<?
					$extern = pathinfo($v, PATHINFO_EXTENSION);
					if ($extern == "zip")
					{
?>
    <a style="margin-left:25px;" href="/filemanager/get?act=unzip&file=<?=$this->path."/".$v;?>" onclick="loading(this); return false;" class="btn btn-xs btn-primary"><i class="icon-share-alt"></i> Разархивировать</a>
<?
					}
					if ($extern == "php" || $extern == "js" || $extern == "css" || $extern == "txt")
					{
?>
    <a style="margin-left:25px;" href="/filemanager/get?act=edit_file&file=<?=$this->path."/".$v;?>" onclick="loading(this); return false;" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Править</a>
<?
					}
					if ($extern == "jpg" || $extern == "gif" || $extern == "png" || $extern == "peg")
					{
?>
    <a style="margin-left:25px;" href="<?=substr($this->path, strlen($_SERVER["DOCUMENT_ROOT"]))."/".$v;?>" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> Показать</a>
<?
					}
				}
				if (!is_file($this->path."/".$v))
				{
?>
    <a href="/filemanager/get?act=zip&folder=<?=$this->path."/".$v;?>" target="_blank" class="btn btn-xs btn-success"><i class="fa fa-download"></i> Скачать архивом</a>
<?
				}
?>
    <a style="margin-left:25px;" href="/filemanager/get?act=del&file=<?=$this->path."/".$v;?>" onclick="del(this); return false;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Удалить</a><?php /*?><span style="font-size:9px;"><?php    echo "[".substr(sprintf('%o', fileperms($this->path."/".$v)), -4)."]"; ?></span> <?php */?></li>
<?
			}
		}
?>
</ul>
<?
	}
	////////////////////////////////////
	// Функция получения контента файла и его редактирование
	////////////////////////////////////
	function open_file_to_edit()
	{
		if (!is_writable($this->file_w))
		{
			$m = "no_save";
		} else { $m="";}
?>
<form id="form1" name="form1" method="post" action="/filemanager/get">
  <input type="submit" name="button" id="button" value="Сохранить"  class="btn btn-send btn-xs" onclick="sen_file_dt(); return false;" <?php if ($m == 'no_save') { ?>disabled="disabled"<?php } ?> />
    <br>
  <input type="hidden" name="file" id="file" value="<?=$this->file_w;?>" />
  <input type="hidden" name="act" id="act" value="save_file" />
  <label for="file_content"></label>
<?
		if ($m == 'no_save')
		{
			echo "<span style=\"color:#FF0000\">ФАЙЛ НЕЛЬЗЯ БУДЕТ СОХРАНИТЬ! У вас недостаточно прав!</span>";
		}

            ?>
            <br/>
            <textarea name="file_content" id="file_content"
                      style="height:100%; min-height:500px; width:80%; position:relative;"><?= file_get_contents($this->file_w); ?></textarea>
            <br/>
            <div class="modal-footer"><input type="submit" name="button" id="buttonSaveAndStop"
                                             value="Сохранить и остаться" class="btn btn-success"
                                             onclick="sen_file_dt(); return false;"
                                             <?php if ($m == 'no_save') { ?>disabled="disabled"<?php } ?> />
                <input type="submit" name="button" id="button" value="Сохранить и закрыть" class="btn btn-send"
                       onclick=" sen_file_dt('true'); return false;"
                       <?php if ($m == 'no_save') { ?>disabled="disabled"<?php } ?> /></div>
        </form>
        <script>
            if (typeof LoadedCodeMirror === "undefined") {
                var LoadedCodeMirror = false;
            }
            if (!LoadedCodeMirror) {
                $('#modal_window').on('shown.bs.modal', function () {
                    if (typeof CodeMirror != "undefined") {
                        const CM = CodeMirror.fromTextArea(document.getElementById('file_content'), {
                            lineNumbers: true, // Нумеровать каждую строчку.
                            matchBrackets: true,
                            mode: "application/x-httpd-php",
                            indentUnit: 2, // Длина отступа в пробелах.
                            indentWithTabs: true,
                            enterMode: "keep",
                            tabMode: "shift",
                            theme: "eclipse"
                        });
                        LoadedCodeMirror = true;

                        function saveEditor() {
                            CM.save();
                        }

                        CM.on('change', saveEditor);
                    } else {
                        var CM = {
                            save: function () {
                                return false;
                            }
                        };

                    }
                });
            }
        </script>
        <?
    }
    ////////////////////////////////////
    // Функция удаления файла
    ////////////////////////////////////
    function del()
    {
        if (is_file($this->file_w)) {
            if (@chmod($this->file_w, 0777)) {
                if (!@unlink($this->file_w)) {
                    die("НЕ СМОГ УДАЛИТЬ ФАЙЛ " . $this->file_w);
                }
            } else {
                die("НЕ СМОГ ИЗМЕНИТЬ ПРАВА ФАЙЛА " . $this->file_w);
            }
        } else {
            $fs = \core\FS::instance();
            $fs->removeFolder($this->file_w, true);
        }
        if (!empty($_GET['back'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            die();

		}
	}
	////////////////////////////////////
	// Функция сохранения данных в файл
	////////////////////////////////////
	function save_in_file()
	{
		if (file_put_contents($this->file_w, $_POST["file_content"]))
		{
			echo "Сохранено успешно";
		}
		else
		{
			echo "Ошибка записи в файл";
		}
	}
}

/**
 * Стартуем файловый менеджер
 */
$file_manager = new file_manager();
die();