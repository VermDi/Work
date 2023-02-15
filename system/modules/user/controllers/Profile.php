<?php
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 26.06.2017
 * Time: 11:13
 */

namespace modules\user\controllers;


use core\Controller;
use core\Html;
use core\Tools;
use Exception;
use modules\user\helpers\UserslHelper;
use modules\user\models\Property;
use modules\user\models\Property_value;
use modules\user\models\USER;
use core\Errors;

/**
 * @property-read Property $propertyModel
 * @property-read Property_value $propertyValueModel
 * @property-read USER $user
 * Class Profile
 * @package modules\user\controllers
 */
class Profile extends Controller
{
	private $user;
	private $propertyModel;
	private $propertyValueModel;

	public function init()
	{
        if (USER::current()->id < 1) { Errors::e500('ERROR 1');}
		$this->setPropertyModel(new Property());
		$this->setPropertyValueModel(new Property_value());
		$this->setUser(new USER());
	}

	public function actionIndex($id = false, $modal = false)
	{
		if (USER::current()->id > 0) {
			$this->user->clear()->getOne(USER::current()->id);
			$users_count = $this->user->getCountChildren();
			if (!$id || $id == USER::current()->id) {
				$avatar     = $this->getAvatar(USER::current()->id);
				$properties = $this->propertyModel->getPropertiesValues(UserslHelper::getProfileFields(), USER::current()->id);
				Html::instance()->setCss('/assets/vendors/jcrop/css/jquery.Jcrop.css');
				Html::instance()->setCss('/assets/vendors/jquery-ui-1.12.1.custom/jquery-ui.css');
				Html::instance()->setJs('/assets/vendors/jcrop/js/jquery.Jcrop.min.js');
				Html::instance()->setJs('/assets/vendors/Inputmask-4.x/js/inputmask.js');
				Html::instance()->setJs('/assets/vendors/Inputmask-4.x/js/inputmask.extensions.js');
				Html::instance()->setJs('/assets/vendors/Inputmask-4.x/js/inputmask.phone.extensions.js');
				Html::instance()->setJs('/assets/vendors/Inputmask-4.x/js/inputmask.numeric.extensions.js');
				Html::instance()->setJs('/assets/vendors/Inputmask-4.x/js/jquery.inputmask.js');
				Html::instance()->setJs('/assets/vendors/Inputmask-4.x/js/phone-codes/phone.js');
				Html::instance()->setJs('/assets/vendors/Inputmask-4.x/js/phone-codes/phone-ru.js');
//				Html::instance()->setJs('/assets/vendors/jquery-ui-1.12.1.custom/i18n/datepicker-ru.js');
				Html::instance()->setJs("/assets/vendors/bootstrap-validator-master/js/validator.min.js");
				Html::instance()->setJs("/assets/vendors/jquery-validation/dist/jquery.validate.min.js");
				Html::instance()->setJs("/assets/vendors/jquery-validation-bootstrap-tooltip-master/jquery-validate.bootstrap-tooltip.min.js");
				Html::instance()->setCss('/assets/modules/user/css/userProfile.css');
				Html::instance()->setCss('/assets/modules/user/css/permissions.css');
				Html::instance()->setJs('/assets/modules/user/js/userProfile.js');
				Html::instance()->setJs('/assets/modules/user/js/userRepository.js');
				Html::instance()->setJs("/assets/modules/user/js/user.js");
				Html::instance()->setJs('/assets/modules/user/js/checks.js');
				Html::instance()->title   = 'Мой профиль';
				Html::instance()->content = $this->render('user/profile.php', ['user' => \modules\user\models\USER::current(), 'properties' => $properties, 'avatar' => $avatar,'users_count'=>$users_count]);
				Html::instance()->renderTemplate($this->config['main_template'])->show();
			} else {
				$user       = $this->user->clear()->getOne($id);
				$avatar     = $this->getAvatar($id);
				$properties = $this->propertyModel->getPropertiesValues(UserslHelper::getProfileFields(), $id);
				if (!$modal) {
					Html::instance()->title   = 'Профиль пользователя ';
					Html::instance()->content = $this->render('user/profileInfo.php', ['user' => $user, 'properties' => $properties, 'avatar' => $avatar, 'users_count' => $users_count]);
					Html::instance()->renderTemplate($this->config['main_template'])->show();
				} else {
					echo "<div class=\"modal-header\">
        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
        <h4 class=\"modal-title\" id=\"myModalLabel\">Профиль пользователя</h4>
      </div>
      <div class=\"modal-body\" id=\"mbody\"><div class='row'> " . $this->render('user/profileInfo.php', ['user' => $user, 'properties' => $properties, 'avatar' => $avatar, 'users_count' => $users_count]) . "</div></div>";
				}

			}
		} else {
			header('Location: /user/login');
		}


		exit;
	}

	public function getAvatar($userId)
	{
		$files = glob(_ROOT_PATH_ . "/public/avatars/user/" . $userId . "/*");
		if (!empty($files) && !empty($files[0])) {
			$info   = pathinfo($files[0]);
			$avatar = "/public/avatars/user/" . $userId . "/" . $info['basename'];
		} else {
			$avatar = "/assets/templates/index/images/noavatar100.png";
		}
		return $avatar;
	}

	public function actionAvatarForm(){
		echo $this->render('profile/avatarLoad.php');
		exit;
	}


	public function actionUpload()
	{
		$targ_w       = $targ_h = 100;
		$jpeg_quality = 90;
		$data         = array();
		if (!extension_loaded('gd')) {
			throw new Exception('Required extension GD is not loaded.');
		};

		$src   = base64_decode(str_replace(' ', '+', preg_replace('#^data:image/[^;]+;base64,#', '', $_POST['value'])));
		$img_r = imagecreatefromstring($src);
		$dst_r = ImageCreateTrueColor($targ_w, $targ_h);
		imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['coords']['x'], $_POST['coords']['y'],
			$targ_w, $targ_h, $_POST['coords']['w'], $_POST['coords']['h']);
		header('Content-type: image/jpeg');
		$structure = '/public/avatars/user/' . USER::current()->id;
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $structure)) {
			if (!mkdir($_SERVER['DOCUMENT_ROOT'] . $structure, 0777, true)) {
				die('Не удалось создать директории...');
			}
		}

		$this->cleanDir($structure);
		$fileName = Tools::generateRandomString();
		if (imagejpeg($dst_r, $_SERVER['DOCUMENT_ROOT'] . $structure . '/' . $fileName . '.jpg', $jpeg_quality)) {
			$data['status'] = 'OK';
			$data['img']    = $structure . "/" . $fileName . ".jpg";

		} else {
			$data['status'] = "ERROR";
		}
		echo json_encode($data);
		exit;
	}

	public function actionChange()
	{
		if (empty($_POST['user']['password'])) {
			$password = "";
			unset($_POST['user']['password']);
		} else {
			$data['password'] = $this->user->generateHashWithSalt($_POST['user']['password']);
			$password         = $data['password'];
		}

		if (USER::current()->id != 1 && !filter_var($_POST['user']['email'], FILTER_VALIDATE_EMAIL)) {
			$_SESSION['errors']['email'] = 'Неверный формат почты';
		}

		if (!empty($_SESSION['errors'])) {
			header('Location: /user/profile');
			exit;
		}
		$current              = $this->user->getOne(['id' => USER::current()->id]);
		$data['id']           = $current->id;
		$data['email']        = $_POST['user']['email'];
		$data['fio']          = $_POST['user']['fio'];
		$data['phone_number'] = $_POST['user']['phone_number'];
		$this->user->save($data);
		if (isset($_POST['user_properties']) && $_POST['user_properties']) {
			foreach ($_POST['user_properties'] as $key => $value) {
				$this->propertyModel->clear()->factory($key);
				$this->propertyValueModel->clear()->factory();
				$this->propertyValueModel->property_id = $key;
				if ($_POST['user_prop_ids'][$key]) {
					$this->propertyValueModel->id = $_POST['user_prop_ids'][$key];
				}
				$this->propertyValueModel->value = $value;

				$this->propertyValueModel->user_id = $current->id;
				$this->propertyValueModel->save();
			}
		}
		if (empty($password)) {
			$this->user->login($_POST['user']['email'], $current->password);
		} else {
			$this->user->login($_POST['user']['email'], $password);
		}
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}

	function cleanDir($dir)
	{
		$files = glob(_ROOT_PATH_ . "/" . $dir . "/*");
		if (count($files) > 0) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					unlink($file);
				}
			}
		}
	}

	/**
	 * @param Property_value $propertyValueModel
	 */
	private function setPropertyValueModel(Property_value $propertyValueModel)
	{
		$this->propertyValueModel = $propertyValueModel;
	}

	/**
	 * @param Property $propertyModel
	 */
	private function setPropertyModel(Property $propertyModel)
	{
		$this->propertyModel = $propertyModel;
	}


	/**
	 * @param USER $user
	 */
	private function setUser(USER $user)
	{
		$this->user = $user;
	}

	public function actionAddUserForm()
	{
		if (USER::current()->level <= 2) {
			Html::instance()->content = $this->render("/subusers/form.php", ['data' => USER::current()]);
			Html::instance()->renderTemplate($this->config['main_template'])->show();
		} else {
			Errors::e500('НЕТ ПРАВ');
		}

	}

	public function actionAddUser()
	{
		$email = trim($_POST['email']);
		$pass  = $_POST['pass'];
		$salt  = substr(sha1($pass), 10, 20) . "\3\1\2\6";
		$pass  = sha1(sha1($pass) . $salt);
		if (USER::instance()->where('email', '=', $email)->getOne()) {
			Errors::e500('ТАКОЙ УЖЕ ЕСТЬ в системе');
			die();
		}
		/**
		 * Смещаем всех выше...
		 */
		USER::instance()->insertNode(USER::current()->id, ['email' => $email, 'password' => $pass]);

		header("Location: /user/profile");
		die();


	}


}