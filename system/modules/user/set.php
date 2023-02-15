<?php
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 05.09.2017
 * Time: 14:31
 */
return [
	'main_template'         => 'index',
	'user_profile_template' => 'index',
	'authForm'              => 'login',
	'registrationForm'      => 'registration',
	'successRegistration'   => 'successRegistration',
	'forgotPasswordForm'    => 'forgotPassword',
	'recoverForm'           => 'recoverForm',
	'error'                 => 'errorToken',
	'authError'             => 'authError',
	'params' =>
		[
			'size_avatar' =>
				[
					'title' => 'Размер аватара',
					'value' => '100x100',
					'help-block'=>'Размеры аватара пользователя ШxВ'
				]
		]
];