<?php
/**
 * Created by PhpStorm.
 * User: noutbuk
 * Date: 14.06.2018
 * Time: 20:26
 */

namespace modules\user\widgets;


use core\Html;

class PermissionForm
{

	public static function printForm($permissions, $user_permissions)
	{
		$arr=[];
		foreach ($permissions as $permission) {
			$explode                                                    = explode('.', $permission->name);
			$arr[$explode[0].".".$explode[1]]['name'] = $explode[0].$explode[1];
			$arr[$explode[0].".".$explode[1]]['items'][] = $permission;
		}
		ksort($arr);
		echo Html::instance()->render(_MOD_PATH_ . "/user/widgets/permissions/form.php", ['permissions' => $arr, 'user_permissions' => $user_permissions]);
	}
}