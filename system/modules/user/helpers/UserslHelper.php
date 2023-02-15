<?php

namespace modules\user\helpers;


use modules\user\models\USER;

class UserslHelper
{
    public static function instance()
    {
        return new UserslHelper;
    }

    public function getChildrensUsersIds()
    {
        $ChildrensUsers = USER::instance()->getChildrensUsers();
        $Cids           = [];
        if (is_array($ChildrensUsers)) {
            foreach ($ChildrensUsers as $ChildrensUser) {
                $Cids[] = $ChildrensUser->id;
            }
        }
        if (!empty($Cids)) {
            return $Cids;
        } else {
            return false;
        }
    }

    public static function getProfileFields()
    {
        return [
            'fio',
            'nickname',
            'phone_number',
            'birthday',
            'gender'
        ];
    }

    public static function getPropertyTypes(){
        return [
            'text'=>'текст',
            'select'=>'список',
        ];
    }
}
