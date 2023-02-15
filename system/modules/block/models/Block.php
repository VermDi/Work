<?php
/**
 * Created by PhpStorm.
 * User: Pash
 * Date: 29.09.2015
 * Time: 18:58
 */

namespace modules\block\models;

use core\App;
use modules\block\widgets\BlocksWidget;
use modules\infoblock\widgets\wInfoblock;
use modules\user\models\User;
use core\Model;


/**
 *
 * @property string id - айди
 * @property string name - название
 * @property string title - название
 * @property string content - содержимое
 * @property integer type - тип
 * @property string rights - права
 * @property integer pid - родительский блок
 * @property integer is_editor_enabled - включение редактора
 * Class Block
 * @package modules\block\models
 */
class Block extends Model
{
    public $table = 'block';
    const EDITOR_ENABLED = 1;
    const EDITOR_DISABLED = 2;

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->name = "";
            $this->title = "";
            $this->content = "";
            $this->type = 0;
            $this->rights = NULL;
            $this->pid = 0;
            $this->is_editor_enabled = self::EDITOR_ENABLED;
        }
        return $this;
    }

    /**
     * @param $value
     * @return Block
     */
    public function findByName($value)
    {
        return $this->where(['name' => $value])->getOne();
    }

    /**
     * @param $value
     * @param $rights
     * @return Block[]
     */
    public function findByNames(array $value, $rights = false)
    {
        $query = $this->where(['name' => $value]);
        $rights && $query->where(['rights&?' => $rights]);
        return $query->getAll();
    }

    public function findByCatId($value)
    {
        return $this->where('cid', $value)->getAll();
    }

    private function isNameExists($value, $excludeId = false)
    {
        $obj = new Block();
        $obj->where('name', '=', trim($value));
        if ($excludeId) {
            $obj->where('id', '<>', $excludeId);
        }
        return $obj->getOne();
    }

    public function validate()
    {
        $result = [];
        if (!strlen(trim($this->name))) {
            $result['name'][] = 'Имя не может быть пустым';
        }

        if ($this->isNameExists(trim($this->name), $this->id)) {
            $result['name'][] = 'Имя уже существует';
        }
        return $result;
    }

    public function beforeSave()
    {
        $this->name = trim($this->name);
        $this->title = trim($this->title);
    }

    public static function render($html)
    {
		if (isset($_GET['wysiwyg']) and \core\User::current()->isAdmin()) {
			return $html;
		}
        preg_match_all('/\{#(.*?)#\}/', $html, $matches);
        $idsArr = [];
        $self = new self();
        if ($matches[1]) {
            $blocks = $self->select('content,name,id')->in('name', array_unique($matches[1]))->getAll();
            /*
             * Получаем массив под замену
             */

            if ($blocks) {
                foreach ($blocks as $k => $v) {
                    $replace[$v->name] = $v->content;
                    $idsArr[$v->name] = $v->id;
                }
            } else {
                return $html;
            }
            unset($blocks);
            $trans = [];
            foreach ($matches[0] as $i => $value) {
                if (class_exists('modules\vizualeditor\controllers\Save') and \core\User::current()->isAdmin()) {
                    $trans[$value] = isset($replace[$matches[1][$i]]) ? '<div data-edit="emind_admin" data-field="content" data-table="block" data-id="' . $idsArr[$matches[1][$i]] . '">' . $replace[$matches[1][$i]] . "</div>" : "";
                } else {
                    $trans[$value] = isset($replace[$matches[1][$i]]) ? $replace[$matches[1][$i]] : "";
                }
                ob_start();
                eval("?>" . $trans[$value] . "<?");
                $trans[$value] = ob_get_contents();
                ob_end_clean();
            }
            $html = strtr($html, $trans);
        }
        if (class_exists('modules\infoblock\widgets\wInfoblock')) {
            $html = wInfoblock::instance()->ReplaceHtml($html);
        }

        if (class_exists('modules\block\widgets\BlocksWidget')) {
            /*
          * Если есть php блоки, то обрабатываем
          */
            $html = BlocksWidget::instance()->getContentInsertBlocks($html);
        }



        return $html;
    }
}