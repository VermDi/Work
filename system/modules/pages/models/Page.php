<?php

namespace modules\pages\models;

use core\Model;
use modules\historian\helpers\History;
use modules\user\models\USER;


/**
 * Class Page
 *
 * @property string id               -
 * @property string title            -
 * @property string meta_keywords    -
 * @property string meta_description -
 * @property string meta_additional  -
 * @property string content          -
 * @property string design           -
 * @property string menu_name        -
 * @property string visible          -
 * @property string url              -
 * @property string create_at        -
 * @property string update_at        -
 * @property string user_id          -
 * @property string level            -
 * @property string left_key         -
 * @property string right_key        -
 * @property string domain           -
 * @property int position            -
 * @property int bg_img              -
 * @property int h1_text             -
 */
class Page extends Model
{
	public $table = 'pages';

	public function factory($id = false)
	{
		if ($id == false or !$this->getOne($id)) {
			$this->id = "";
			$this->title = "";
			$this->meta_keywords = "";
			$this->meta_description = "";
			$this->meta_additional = "";
			$this->content = "";
			$this->design = null;
			$this->menu_name = null;
			$this->visible = 1;
			$this->url = "";
			$this->create_at = "";
			$this->update_at = "";
			$this->user_id = null;
			$this->level = "";
			$this->left_key = "";
			$this->right_key = "";
			$this->domain = 0;
			$this->position = null;
			$this->bg_img = null;
			$this->h1_text = null;


		}
		return $this;
	}

	/**
	 * Прежде чем вставлять  сделаем урл, и проставим ключи границ
	 *
	 * @return bool
	 */
	public function beforeInsert()
	{
		//Вычисляем границы если они не переданы
		if (empty($_POST['left_key'])) { //если границы переданы
			$time = new self();
			$time->clear()->select('max(right_key) as m')->getOne();
			$this->left_key = $time->m + 1;  //взводим левую границу
			$this->right_key = $time->m + 2; //взводим правую границу
			$this->level = 0;
			$time->clear()->select('max(position) as pos')->getOne();
			$this->position = $time->pos + 1;
		}
		$this->update_at = date("Y-m-d H:i:s", time());
		//время создания
		$this->create_at = date("Y-m-d H:i:s", time());
		//генерация и проверка урла
		$this->url = $this->prepareURL($this->url);
		$user_id = USER::current()->id;
		$user_id = "";
		//взводим создателя записи
		if (!empty($user_id)) {
			$this->user_id = USER::current()->id;
		} else {
			$this->user_id = null;
		}
		//Если не заполнили название в меню заполняем
		if (empty($this->menu_name)) {
			$this->menu_name = $this->title;
		}
		//возвращаем тру если все ок
		return true;

	}

	/**
	 * Прежде чем обновлять запись проверим что url есть и проерим его
	 *
	 * @return bool
	 */
	public function beforeUpdate()
	{
		if (class_exists('modules\historian\helpers\History')) {
			History::set('pages', $this->id, self::getOne($this->id));
		}
		$user_id = USER::current()->id;
		if (!empty($user_id)) {
			$this->user_id = USER::current()->id;
		} else {
			$this->user_id = null;
		}

		$this->url = $this->prepareURL($this->url);

		if (strlen($this->menu_name) < 1) {
			$this->menu_name = $this->title;
		}
		$this->update_at = date("Y-m-d H:i:s", time());
		return true;
	}

	public function beforeSave()
	{
		if (substr($this->url, 0, 1) == "/" and strlen($this->url)>2) {
			$this->url = substr($this->url, 1);
		}
	}


	/**
	 * Возвращает URL подготовленный
	 *
	 * @param $url
	 *
	 * @return string
	 */
	private function prepareURL($url)
	{
		if (empty($url)) {
			$url = \URLify::filter($this->title);
		} //Если ссылку не дали делаем ее из тайтла
		$u = explode("/", $url);
		foreach ($u as $k => $v) {
			$u[$k] = \URLify::filter($v);
		}
		return implode('/', $u);
	}

	/**
	 * Переведено в сервис
	 *
	 * @return array|object
	 * @deprecated
	 */
	public function GetForList()
	{
		return $this->clear()->select('id,menu_name as name,left_key,right_key, level')->orderBy('position DESC')->getAll();

	}

	/**
	 * Получает объект страницы по урлу
	 *
	 * @return bool|mixed|\PDOStatement|\stdClass|string
	 */
	public function GetByUrl($url, $host = false)
	{
		$m = $this->clear()->select(' * ')
				  ->where("url", "=", $url)
				  ->where("visible", "=", 1);
		if ($host !== false) {
			$m->where('domain', '=', $host);
		}
		return $m->getOne();

	}

	public function beforeDelete()
	{
		if (class_exists('modules\historian\helpers\History')) {
			History::set('pages', $this->id, self::getOne($this->id));
		}
		return true;
	}


}