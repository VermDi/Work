<?php
/**
 * Create by e-Mind Studio
 * User: Семья
 * Date: 29.04.2018
 * Time: 16:35
 */

namespace modules\admin\models;


use core\Model;

class Pages extends Model
{
   public $table="pages";

   public $id;
   public $name;
   public $crack;
   public $url;

}