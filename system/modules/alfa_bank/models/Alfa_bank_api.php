<?php
namespace modules\alfa_bank\models;

use core\Model;

/**
 * Class Alfa_bank_api
* @property string id - 
  
*/
class Alfa_bank_api extends Model
{ 
    public $table = 'alfa_bank_api';
    public function factory($id=false)
    {
        if ($id == false or !$this->getOne($id)) 
        {            $this->id = ""; 
 
        }
        return $this;
    }	
}