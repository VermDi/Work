<?php

namespace modules\amocrm\models;

use core\Model;

/**
 * Class AmoCrmSettings
 *
 * @property int id -
 * @property string client_id -
 * @property string client_secret-
 * @property string redirect_url -
 */
class AmoCrmSettings extends Model
{
 public $table = 'amocrm_settings';

}