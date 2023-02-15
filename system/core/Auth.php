<?php
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 8:59
 */

namespace core;


class Auth extends Obj
{
    public static $instance;
    function __construct()
    {
       if (!self::$instance){
           self::$instance = $this;
       }
    }
    function isAuth($rights)
    {
        if ($_SESSION['user']['rights'] & $rights) {
            return true;
        } else {
            return false;
        }
    }
    public function logout()
    {
        unset($_SESSION["user"]); //очишаем сессию в целях безопасности
        $_SESSION["user"]['rights'] = 0x8; // ставим новые права
    }

    public static function startUserSession()
    {
        \modules\user\models\USER::initFirst();
        Event::trigger('core.user.init', \core\User::current());
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $_SESSION['backlink'] = htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES);
        }
    }

}