<?php
/**
 * Create by e-Mind Studio
 * User: dulentcov-smishko
 * Date: 19.10.2018
 * Time: 13:07
 */

namespace modules\debuger\widgets;


use core\Html;
use core\User;

/**
 * Класс отвечает за отображение дебаг панели
 * Class DebugPanel
 * @package modules\debuger\widgets
 */
class DebugPanel
{
    public static $instance;
    private $panel = "";

    public static function instance()
    {
        return static::$instance ?: new static();
    }

    /**
     * Отрисовывает панель только для админа
     */

    public function render()
    {
        if (User::current()->isAdmin()) {
            $this->panel = Html::instance()->render(__DIR__ . '/../templates/DebugPanel.php');
        }

    }

    /**
     * Оотображает панель
     */
    public function show()
    {
        $this->render();
        echo $this->panel;
    }
}