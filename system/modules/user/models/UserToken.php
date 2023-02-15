<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 14:39
 */

namespace modules\user\models;

use core\Model;

class UserToken extends Model
{

    public $table = "user_token";
    public static $instance;

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function factory($id = false)
    {
        if ($id == false or !$this->where('id', '=', $id)->getOne()) {
            $this->id      = "";
            $this->value   = "";
            $this->user_id = "";
            $this->email   = "";
            $this->type    = "";
            $this->time    = "";
        }
        return $this;
    }

    public function getByCode($code)
    {
        $this->where('value', '=', $code)->getOne();
        return $this;
    }
}