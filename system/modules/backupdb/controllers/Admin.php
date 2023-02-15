<?
/**
 * Created by sslab
 * Date: 30.03.2017
 * Time: 21:09
 */

namespace modules\backupdb\controllers;

use core\Controller;
use core\Html;
use modules\backupdb\services\BackupDbMySQL as BackupDbMySQL;

class Admin extends Controller
{
	const PATH_BACKUP = 'data/BackupDbMySQL';

	public function actionIndex()
	{
		$backupListTemp = $backupList = [];
		$this->getListFiles(self::PATH_BACKUP, $backupListTemp);

		foreach ($backupListTemp as $k => $d)
		{
			$path = _ROOT_PATH_.'/'.$d;
			$file = basename($path);
			$extension = pathinfo($path, PATHINFO_EXTENSION);
			$date = str_replace('.'.$extension, '', $file);
			$backupList[$date] = $path;
		}
		$HTML = Html::instance();
		krsort($backupList);
        $HTML->title = 'Бэкапы БД';
        $HTML->content = $this->render('/index.php', [
			'backupList' => $backupList
		]);
        $HTML->renderTemplate("@admin")->show();
		return true;
	}

	public function actionCreate()
	{
		$filePath = self::PATH_BACKUP.'/'.time().'.sql';
		//echo 'Бэкап БД будет сохранен в: '.$filePath.'<br>';
		try
		{
			$dump = new BackupDbMySQL('mysql:host='._DB_SERVER_.';dbname='._DB_NAME_.'', _DB_USER_, _DB_PASS_);
			$dump->start($filePath);
		} catch (\Exception $e)
		{
			echo 'Ошибка при создании бэкапа: '.$e->getMessage();
		}
		if (file_exists($filePath))
		{
			//echo 'Бэкап успешно создан<br>';
		}
		else
		{
			echo 'Бэкап не найден<br>';
		}
		$this->redirect('/backupdb/admin/');
	}

	public function actionDelete($id)
	{
		$filePath = self::PATH_BACKUP.'/'.$id;
		unlink($filePath);
		$this->redirect('/backupdb/admin/');
	}

	public function actionRestore($id)
	{
		$filePath = self::PATH_BACKUP.'/'.$id;
		$db = _DB_NAME_;
		if (file_exists($filePath))
		{
			$dump = new BackupDbMySQL('mysql:host='._DB_SERVER_.';dbname='.$db.'', _DB_USER_, _DB_PASS_);
			$dump->restore($filePath);
		}
		$this->redirect('/backupdb/admin/');
	}

	private function getListFiles($folder, &$all_files)
	{
		if (is_dir($folder))
		{
			$fp = opendir($folder);
			while ($cv_file = readdir($fp))
			{
				if (is_file($folder."/".$cv_file))
				{
					$all_files[] = $folder."/".$cv_file;
				}
				elseif ($cv_file != "." && $cv_file != ".." && is_dir($folder."/".$cv_file))
				{
					self::GetListFiles($folder."/".$cv_file, $all_files);
				}
			}
			closedir($fp);
		}
	}
}