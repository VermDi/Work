<?php
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 8:58
 */

namespace core;


/**
 * Class Rights - проверяет права в системе, работает с классом юзер и классом auth если то требуется
 * @package core
 */
class Rights
{
    protected static $instance;

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Проверка правк доступа к контроллеру или модулю или экшену
     * @param $module
     * @param $controller
     * @param string $action
     * @return bool
     */
    function checkAccess($module, $controller, $action = '')
    {
        $controller = strtolower($controller);
        $action     = strtolower($action);
        $rights     = $this->getRightsData($module);

        $result     = false;
        $user       = \modules\user\models\USER::current();
        if ($rights && isset($rights[$controller]) && is_array($section = $rights[$controller])) {
            if (isset($section['permission']) && is_array($section['permission'])) {
                $result = in_array('*', $section['permission']) || $user->can($section['permission']);
            }
            if (!$result && isset($section['role']) && is_array($section['role'])) {
                $result = in_array('*', $section['role']) || $user->is($section['role']);
            }
            if ($action && isset($section['actions'][$action]) && is_array($section = $section['actions'][$action])) {
                $result = false;
                if (isset($section['permission']) && is_array($section['permission'])) {
                    $result = in_array('*', $section['permission']) || $user->can($section['permission']);
                }
                if (!$result && isset($section['role']) && is_array($section['role'])) {
                    $result = in_array('*', $section['role']) || $user->is($section['role']);
                }
            }
        }
        return $result;
    }

    /**
     * Получение прав доступа к модулю
     * @param $module
     * @return bool|mixed
     */
    protected function getRightsData($module)
    {
        $fs   = FS::instance();
        $real = implode(DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR, explode(DIRECTORY_SEPARATOR, $module));
        $path = App::$ModulesPath . DIRECTORY_SEPARATOR . $real . DIRECTORY_SEPARATOR . 'rights.php';
        $data = $fs->isFile($path) ? include $path : false;
        $data = is_array($data) ? $data = $this->arrayChangeKeyCaseRecursive($data) : false;
        return $data;
    }

    /**
     * Рекурсивная обработка массива
     * @param $arr
     * @return array
     */
    private function arrayChangeKeyCaseRecursive($arr)
    {
        return array_map(
            function ($item) {
                if (is_array($item)) {
                    $item = $this->arrayChangeKeyCaseRecursive($item);
                }
                return $item;
            }, array_change_key_case($arr)
        );
    }


}