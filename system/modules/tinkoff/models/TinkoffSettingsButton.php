<?php

namespace modules\tinkoff\models;

use core\App;
use core\Model;

/**
 * Class TinkoffSettingsButton
 *
 * @property int id -
 * @property string SHOP_ID -
 * @property string SHOWCASE_ID-
 * @property string promoCode -
 * @property string view -
 */
class TinkoffSettingsButton extends Model
{
    public $table = 'tinkoff_settings_button';

    static function getSettings($options = false)
    {
        $q = TinkoffSettingsButton::instance();
        if (!isset($options['select'])) {
            $options['select'] = TinkoffSettingsButton::instance()->table . '.*';
        }
        $q = $q->select($options['select']);

        if (isset($options['getOne'])) {
            $q = $q->getOne();
        } else {
            $q = $q->getAll();
        }
        return $q;

    }
}