<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 09.06.2017
 * Time: 17:55
 */

namespace modules\pages\controllers;

use core\App;
use core\Controller;
use core\Errors;
use core\Html;
use core\Obj;
use core\Parameters;
use core\StaticCache;
use core\Tools;
use core\User;
use modules\pages\helpers\Nested;
use modules\pages\models\Page;

class Index extends Controller
{
	function getObj($type)
	{
		$obj = new \stdClass();
		$obj->type = $type;
		$obj->id = Tools::passFromString(4, 6);
		$obj->isActive = false;
		$obj->data = [];
		return $obj;

	}

	public function getIndex($a = "*")
	{
		//EXAMPLE
//		StaticCache::on();
		/* Мульти доменность ? */
		$conf = $this->settings();
		$host = false;
		if ($conf['isMultiDomains'] !== 1) {
			$host = false;
		}
		/* /Конец мультидоменноси */

		if (App::$url['path'] != "/") {
			$url = substr(App::$url['path'], 1);
		} else {
			$url = "/";
		}

		//------------------------------------------
		// НЕТ страницы, возвращаем 404 ошибку
		//------------------------------------------
		if (!$data = Page::instance()->GetByUrl($url, $host)) {
			Errors::e404();
		}
		/*
		 * Блок обратной совместимости
		 */

		if (strpos(substr($data->content, 0, 5), "[") === false) {
			$arr = [];
			$row = $this->getObj("row");
			$block = $this->getObj('block');

			$txtAreaBlock = $this->getObj("textAreaBlock");
			$txtAreaBlock->data = $data->content;
			$block->data[] = $txtAreaBlock;
			$row->data[] = $block;
			$arr[] = $row;
			$data->content = json_encode($arr);

		}
		if (strpos($data->content, "\"content\":") !== false) {
			$arr = [];
			$temp = json_decode($data->content);
			foreach ($temp as $item) {
				if ($item->type == "code") {
					$row = $this->getObj("row");
					$block = $this->getObj('block');
					$codeBlock = $this->getObj("codeBlock");
					$codeBlock->data = htmlspecialchars_decode($item->content);
					$block->data[] = $codeBlock;
					$row->data[] = $block;
					$arr[] = $row;
				}
				if ($item->type == "wysiwyg") {
					$row = $this->getObj("row");
					$block = $this->getObj('block');
					$codeBlock = $this->getObj("textAreaBlock");
					$codeBlock->data = $item->content;
					$block->data[] = $codeBlock;
					$row->data[] = $block;
					$arr[] = $row;
				}
			}
			$data->content = json_encode($arr);
		}
		/*
		 * /Блок обратной совместимости
		 */
		if (isset($_GET['wysiwyg']) and User::current()->isAdmin()) {
			html()->setJs("/assets/vendors/Jquery/jquery-3.2.1.min.js");
			html()->setJs("/assets/vendors/ckeditor/ckeditor.js");
			html()->setJs("/assets/vendors/ckeditor/adapters/jquery.js");
			html()->setCss("/assets/modules/pages/js/builder.css");
			html()->setJs("/assets/modules/pages/js/builder.js");
			html()->setJs("/assets/modules/pages/js/frontwysiwyg.js");
			html()->setJs("document.addEventListener(\"DOMContentLoaded\", function(){ window.builder = EMB(document.getElementById('ContentZoneInForm'));builder.setData(" . $data->content . ");});", false, true);
			$data->content = " <div id=\"ContentZoneInForm\"></div>";
			$data->content .= "<button type='submit' id='sendPageData' data-id='" . $data->id . "'>Сохранить изменения</button>";
		} else {
			html()->setCss("/assets/modules/pages/js/builder-disabled.css"); //стили билдера для отключенного билдера
			$p = $this->prepareContent($data->content);
			if (is_array($p)) {
				$data->content = implode("", $p);
			} else {
				$data->content = $p;
			}


			//------------------------------------------
			// Страница есть, а предки есть? Формируем хлебные крошки
			//------------------------------------------
			if ($data->level > 1) {
				if ($urls = Page::instance()
								->clear()
								->where('left_key', '<', $data->left_key)->where('right_key', '>', $data->right_key)
								->where('level', '<', $data->level)
								->getAll()
				) {

					foreach ($urls as $k => $v) {
						if ($v->level != 0) {
							$all[$v->level] = "<a href='/" . $v->url . "'>" . $v->menu_name . "</a>";
						}
					}
					sort($all);
					$data->content = "<ul class='breadcrumb'><li>" . implode(" </li><li>", $all) . "</li></ul>" . $data->content;
				}
			}
		}


		//------------------------------------------
		// Все пусто ?
		//------------------------------------------
		if (empty($data)) {
			Errors::e404();
			die();
		}
		html()->title = $data->title;
		html()->menu_name = $data->menu_name;
		html()->h1_text = $data->h1_text;
		html()->bg = $data->bg_img;
		html()->meta = "<meta name=\"keywords\" content=\"" . $data->meta_keywords . "\" /> <meta name=\"description\" content=\"" . $data->meta_description . "\" />";
		html()->content = $data->content;

		// Включить для статики
		// StaticCache::on();
		html()->renderTemplate($data->design)->show();
	}

	public function printBuilderContent($element)
	{
		$str = "";

		foreach ($element as $item) {
			if ($item->type == 'row') {
				$str .= "<div class='rowLine'>";
				if (!empty($item->data)) {
					$str .= $this->printBuilderContent($item->data);
				}
				$str .= "</div>";
			}
			if ($item->type == 'block') {
				$str .= "<div class='block'>";
				if (!empty($item->data)) {
					$str .= $this->printBuilderContent($item->data);
				}
				$str .= "</div>";
			}
			if ($item->type == 'code' && !is_array($item->data)) {

				$str .= "" . html_entity_decode($item->data, ENT_QUOTES) . "";
			}
			if ($item->type == 'wysiwyg' && !is_array($item->data)) {
				$str .= "" . $item->data . "";
			}

		}
		return $str;

	}

	private function prepareContent($data)
	{
		$pData = json_decode($data);
		if (!is_array($pData)) {
			return $data;
		} else {
			return $this->printBuilderContent($pData);
		}
	}

}