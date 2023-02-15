<?

namespace modules\user\controllers;

use core\Controller;
use core\Db;
use core\Tools;
use core\Errors;
use core\Html;

use modules\user\models\Property_value;
use modules\user\models\USER;
use modules\user\models\Role;
use modules\user\models\UserPermission;
use modules\user\models\Permission;
use modules\user\models\UserRoles as UserRole;
use modules\user\models\UserToken;
use modules\user\services\UserRoles;

/**
 * @property-read USER $user
 * @property-read Role $role
 * @property-read UserRole $userRole
 * @property-read Permission $permission
 * @property-read UserPermission $userPermission
 * @property-read UserRoles $userRoleService
 * Class Index
 * @package modules\user\controllers
 */
class Index extends Controller
{
	const TEMPLATE_TYPE_AUTH    = 'auth';
	const TEMPLATE_TYPE_PROFILE = 'profile';
	const TEMPLATE_TYPE_ADMIN   = 'admin';

	private $role;
	private $userRole;
	private $permission;
	private $userPermission;
	private $userRoleService;

	function init()
	{
		$this->setUser(new USER());
		$this->setRole(new Role());
		$this->setUserRole(new UserRole());
		$this->setPermission(new Permission());
		$this->setUserPermission(new UserPermission());
	}

	public function actionIndex($filter = false)
	{
		Html::instance()->title = 'Список пользователей';
		Html::instance()->setCss('/assets/vendors/datatables/css/dataTables.bootstrap.min.css');
		Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
		Html::instance()->setJs("/assets/vendors/datatables/js/dataTables.bootstrap.js");
		Html::instance()->setJs("/assets/vendors/jquery-validation/dist/jquery.validate.min.js");
		Html::instance()->setJs("/assets/vendors/jquery-validation-bootstrap-tooltip-master/jquery-validate.bootstrap-tooltip.min.js");
		Html::instance()->setJs("/assets/modules/user/js/userList.js");
		Html::instance()->setJs("/assets/modules/user/js/user.js");
		Html::instance()->setJs("/assets/modules/user/js/checks.js");
		Html::instance()->setJs("/assets/modules/user/js/permissionsTabs.js");
		Html::instance()->setCss("/assets/modules/user/css/userList.css");
		Html::instance()->setCss("/assets/modules/user/css/permissions.css");

		$count_new_users = 0;
		if (isset(json_decode($_SESSION['user'])->admin_activity) && json_decode($_SESSION['user'])->admin_activity->login_at)
			$count_new_users = $this->user->clear()->select(['count(id) as count_id'])->where('create_at', '>', json_decode($_SESSION['user'])->admin_activity->login_at)->getOne()->count_id;
		if ($filter && $filter == 'new') {
			$users = $this->user->clear()->where('create_at', '>', json_decode($_SESSION['user'])->admin_activity->login_at)->getAll();
		} else {
			$users = $this->user->clear()->getAll();
		}

		Html::instance()->content = $this->render('user/index.php', ['users' => $users, 'count_new_users' => $count_new_users]);
		Html::instance()->renderTemplate("@admin")->show();
		exit;
	}

	public function actionLogin($ajax = false)
	{
		if ($this->app->request->redirect == '/user/login' || $this->app->request->redirect == '/user/logout' || empty($this->app->request->redirect)) {
			$this->app->request->redirect = '/';
		}

		if (!empty($_POST)) {
			$result = [];
			if ($this->user->login($_POST['email'], $this->user->generateHashWithSalt($_POST['password']))) {
				if (!$ajax) {
					header('Location: ' . $this->app->request->redirect);
					exit;
				} elseif (empty($result)) {
					$result['status']   = "OK";
					$result['redirect'] = $this->app->request->redirect;

				}
			} else {
				if (!$ajax) {
					Html::instance()->title = 'Ошибка авторизации';
					$this->renderTemplate($this->config['authError'], self::TEMPLATE_TYPE_AUTH);
					exit;
				} else {
					$result['status']  = "ERROR";
					$result['message'] = "Ошибка авторизации. Неверный логин/пароль.";
				}


			}
			echo json_encode($result);
		} else {
			Html::instance()->title = 'Авторизация';
			$this->renderTemplate($this->config['authForm'], self::TEMPLATE_TYPE_AUTH);
		}

		exit;
	}

	public function actionLogout()
	{
		if ($this->user->logout()) {
			header('Location: /');
			exit;
		}
	}

	public function actionRegistration()
	{
		$this->renderTemplate($this->config['registrationForm'], self::TEMPLATE_TYPE_AUTH);
		exit;
	}

	public function postRegistrate()
	{
		$url    = parse_url($_SERVER['HTTP_REFERER']);
		$errors = false;
		if ($url['path'] != "/user/registration" && $url['path'] != "/user/registrate") {
			Errors::e500();
		}
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			Html::instance()->title = 'Регистрация';
			$this->renderTemplate($this->config['registrationForm'], self::TEMPLATE_TYPE_AUTH, ['error' => 'Неверный формат почты.', 'email' => $_POST['email'], 'fio' => $_POST['fio'], 'phone_number' => $_POST['phone_number']]);
			$errors = true;
		}

		if ($_POST['password'] != $_POST['password_repeat']) {
			Html::instance()->title = 'Регистрация';
			$this->renderTemplate($this->config['registrationForm'], self::TEMPLATE_TYPE_AUTH, ['error' => 'Пароли не совпали.', 'email' => $_POST['email'], 'fio' => $_POST['fio'], 'phone_number' => $_POST['phone_number']]);
			$errors = true;
		}

		$data['email']        = $_POST['email'];
		$data['fio']          = $_POST['fio'];
		$data['phone_number'] = preg_replace('/[^0-9]/', '', $_POST['phone_number']);;
		$password             = $_POST['password'];
		$data['password']     = $this->user->generateHashWithSalt($password);
		$data['token']        = Tools::generateRandomString();
		if (!$errors) {
			if ($id = $this->user->insertNode(1, $data)) {
//				$role = new Role();
//				$role->addRolesToUser($id, ['company_admin']);
				$permission = new Permission();
				$permission->addAllPermissionToUser($id);
				$mail_params['server']         = _SMTP_HOST_;
				$mail_params['user']           = _SMTP_LOGIN_;
				$mail_params['password']       = _SMTP_PASSWORD_;
				$mail_params['port']           = _SMTP_PORT_;
				$mail_params['to_email']       = $data['email'];
				$mail_params['to_name']        = $data['email'];
				$mail_params['from_email']     = _ROOT_EMAIL_;
				$mail_params['from_name']      = _ROOT_EMAIL_;
				$mail_params['reply_to_email'] = _ROOT_EMAIL_;
				$mail_params['reply_to_name']  = _ROOT_EMAIL_;
				$mail_params['title']          = 'Успешная регистрация на сайте ' . _BASE_DOMAIN_;
				$mail_params['message']        = 'Вы успешно прошли регистрацию на сайте. Чтобы авторизоваться нажмите <a href="http://' . ((_WWW_) ? "www." : "") . _BASE_DOMAIN_ . '/user/login">сюда</a>.';
				Tools::sendSMTPmail($mail_params);
				$mail_params['to_email'] = _ROOT_EMAIL_;
				$mail_params['to_name']  = _ROOT_EMAIL_;
				$mail_params['title']    = 'Зарегистрирован новый пользователь на сайте ' . _BASE_DOMAIN_;
				$mail_params['message']  = 'Зарегистрирован новый пользователь с e-mail: ' . $data['email'];
				Tools::sendSMTPmail($mail_params);
				header('Location: /user/successregistration');
				exit;
			} else {
				Html::instance()->title = 'Регистрация';
				$this->renderTemplate($this->config['registrationForm'], self::TEMPLATE_TYPE_AUTH, ['error' => 'Ошибка регистрации. Данный E-mail уже зарегистрирован.', 'email' => $_POST['email'], 'fio' => $_POST['fio'], 'phone_number' => $_POST['phone_number']]);
			};
		}

		exit;
	}

	public function actionSuccessRegistration()
	{
		Html::instance()->title = 'Успешная регистрация';
		$this->renderTemplate($this->config['successRegistration'], self::TEMPLATE_TYPE_AUTH);
	}

	public function getToken($token)
	{
		if (empty($token)) {
			Errors::e500('НЕТ ДАННЫХ');
		}
		$token = UserToken::instance();
		$token->getByCode(intval($_POST['token']));
		if ($token->id < 1) {
			Errors::e500('НЕВЕРНО');
		}

		$mUser = new User();
		$mUser->factory();
		$mUser->email     = $token->email;
		$mUser->password  = substr(md5(time()), 0, 5);
		$mUser->create_at = date("Y-m-d H:i:s", time());
		$mUser->token     = md5($token->email . _SECRET_KEY_ . time());
		if ($mUser->save()) {
			mail($token->email, "EE", "eee");
		}

		exit;
	}


	public function actionForgotPassword($email = false)
	{
		$this->renderTemplate($this->config['forgotPasswordForm'], self::TEMPLATE_TYPE_AUTH, ['email' => $email]);

	}

	public function actionForgotPass()
	{
		$result = [];
		if ($this->user->checkMail($_POST['email'])) {
			$this->user->clear()->where(['email' => $_POST['email']])->getOne();
			if (empty($this->user->token)) {
				$this->user->token = Tools::generateRandomString();
				USER::instance()->save(['id' => $this->user->id, 'token' => $this->user->token]);

			}

			if (strtolower(_MAIL_DRIVER_) == 'smtp') {
				$settings = [
					'server'         => _SMTP_HOST_,
					'port'           => _SMTP_PORT_,
					'user'           => _SMTP_LOGIN_,
					'password'       => _SMTP_PASSWORD_,
					'to_email'       => $this->user->email,
					'to_name'        => $this->user->email,
					'from_email'     => _ROOT_EMAIL_,
					'from_name'      => _ROOT_EMAIL_,
					'reply_to_email' => _ROOT_EMAIL_,
					'reply_to_name'  => _ROOT_EMAIL_,
					'title'          => 'Данные для восстановления пароля от ' . date("Y-m-d H:i:s"),
					'message'        => "<p>Вы или кто то иной, запросили восстановление пароля на сайте " . _SMTP_HOST_ . "</p><p>
                    Если это делали не вы, то проигнорируйте письмо. </p><p>
                    Иначе перейдите по <a href='http://" . $_SERVER['HTTP_HOST'] . "/user/recover/" . $this->user->token . "'>ссылке</a> для ввода нового пароля.
                    </p><p>
                    Если это ваш email: " . _SMTP_LOGIN_ . "  но вы не регистрировались на сайте, сообщите нам на адресс support@realty-bay.ru и мы удалим ваш аккаунт.
                    </p><p>
                    С уважением поддержка realty-bay.ru</p>" . date("d-m-Y H:i:s")

				];
				if (Tools::sendSMTPmail($settings)) {
					$result = ['success' => true, 'msg' => 'Письмо с дальнейшими инструкциями успешно отправлено.'];

				} else {
					$result = ['success' => false, 'msg' => 'Не удалось отправить письмо с инструкциями. Повторите попытку позже.'];
				}
			}
			if (_MAIL_DRIVER_ == 'mail') {
				if (Tools::sendMail($this->user->email, 'Данные для восстановления пароля', "Перейдите по <a href='http://" . $_SERVER['HTTP_HOST'] . "/user/recover/" . $this->user->token . "'>ссылке</a> для ввода нового пароля.")) {
					$result = ['success' => true, 'msg' => 'Письмо с дальнейшими инструкциями успешно отправлено.'];
				} else {
					$result = ['success' => false, 'msg' => 'Не удалось отправить письмо с инструкциями. Повторите попытку позже.'];
				}
			}

		} else {
			$result = ['success' => false, 'msg' => 'Такого пользователя не существует. Проверьте вводимые данные.'];
		}
		echo json_encode($result);
		exit;
	}

	public function getRecover($token)
	{
		if ($this->user->existByParams(['token' => $token])) {
			$this->renderTemplate($this->config['recoverForm'], self::TEMPLATE_TYPE_AUTH, ['token' => $token]);
		} else {
			$this->renderTemplate($this->config['error'], self::TEMPLATE_TYPE_AUTH);
		}
		exit;
	}

	public function actionResetPassword()
	{
		$result = [];
		if (!empty($_POST)) {
			if ($this->user->existByParams(['token' => $_POST['token']])) {
				$this->user->clear()->where(['token' => $_POST['token']])->getOne();
				if ($_POST['password'] === $_POST['password_repeat']) {
					$this->user->password = $this->user->generateHashWithSalt($_POST['password']);
					$this->user->token    = Tools::generateRandomString();
					$this->user->save();
					$result = ['success' => true, 'msg' => 'Пароль успешно сменен. Сейчас вы будете перенаправлены на страницу входа.'];
				} else {
					$result = ['success' => false, 'msg' => 'Пароли не совпали.'];
				}
			}
		}
		echo json_encode($result);
		exit;
	}

	public function actionCheckemail()
	{
		$user = $this->user->count('id', 'count_id')->where(['email' => $_POST['email']])->getOne();
		if ($user->count_id > 0 && USER::current()->email != $_POST['email']) {
			echo true;
		} else {
			echo false;
		}
		exit;
	}

	public function actionCheckPhone()
	{
		$phone = str_replace(["+", ")", "(", "-"], "", $_POST['phone']);
		$user  = $this->user->count('id', 'count_id')->where(['phone_number' => $phone])->getOne();
		if ($user->count_id > 0) {
			$_SESSION['recover_phone_number'] = $_POST['phone'];
			echo true;
		} else {
			echo false;
		}
		exit;
	}

	public function actionAdd()
	{
		if (USER::current()->id == 1) {
			$roles       = $this->role->getAll();
			$permissions = $this->permission->getAll();
		} else {
			$roles       = $this->role->select(['role.id as id', 'role.name as name', 'role.description as description'])->leftJoin($this->userRole->table, $this->userRole->table . ".role_id", $this->role->table . ".id")->where([$this->userRole->table . ".user_id" => USER::current()->id])->getAll();
			$permissions = $this->permission->in('id', UserPermission::instance()->select(['permission_id'])->where(['user_id' => USER::current()->id]))->getAll();
		}
		echo $this->render('user/form.php',
			[
				'user'        => USER::factory(),
				'roles'       => $roles,
				'permissions' => $permissions
			]);
	}

	public function actionEdit($id)
	{
		if (USER::current()->id == 1) {
			$roles       = $this->role->getAll();
			$permissions = $this->permission->getAll();
		} else {
			$roles       = $this->role->select(['role.id as id', 'role.name as name', 'role.description as description'])->leftJoin($this->userRole->table, $this->userRole->table . ".role_id", $this->role->table . ".id")->where([$this->userRole->table . ".user_id" => USER::current()->id])->getAll();
			$permissions = $this->permission->in('id', UserPermission::instance()->select(['permission_id'])->where(['user_id' => USER::current()->id]))->getAll();
		}
		echo $this->render('user/form.php',
			[
				'user'        => USER::factory($id),
				'roles'       => $roles,
				'permissions' => $permissions
			]);
	}

	public function actionSave()
	{
		if (!empty($_POST)) {
			$parent_id = $_POST['parent_id'];
			unset($_POST['parent_id']);
			if (!(isset($_POST['super_admin'])) && empty($parent_id)) {
				$current   = USER::current();
				$parent_id = $current->id;
			} elseif ($_POST['super_admin']) {
				$parent_id = 0;
			}
			$data['id']      = $_POST['id'];
			$data['email']   = $_POST['email'];
			$data['blocked'] = (!empty($_POST['blocked'])) ? USER::BLOCKED_YES : USER::BLOCKED_NO;
			if ($_POST['id']) {
				if (!empty($_POST['password'])) {
					$data['password'] = $this->user->generateHashWithSalt($_POST['password']);
				}
				$this->user->save($data);
				$id = $_POST['id'];
			} else {
				$password         = (!empty($_POST['password'])) ? $_POST['password'] : Tools::passGenerate();
				$data['password'] = $this->user->generateHashWithSalt($password);
				$data['token']    = Tools::generateRandomString();
				$id               = $this->user->insertNode($parent_id, $data);
				if ($parent_id == 0) {
					$permission = new Permission();
					$permission->addAllPermissionToUser($id);
				}

				Tools::sendMail($data['email'], 'Успешная регистрация на сайте ' . _BASE_DOMAIN_, '. Вы успешно прошли регистрацию на сайте. Ваш логин: ' . $data['email'] . '. Ваш пароль: ' . $password . '<br/> Чтобы авторизоваться нажмите <a href="' . _BASE_DOMAIN_ . '/user/login">сюда</a>.');
			}
			if (!empty($_POST['role'])) {
				$this->userRoleService = new UserRoles();
				$this->userRoleService->clearUserRoles($id);
				foreach ($_POST['role'] as $role) {
					$this->userRoleService->addUserRole($id, $role);
				}

			}
			if (!empty($_POST['permission'])) {
				$this->userPermission->delete(['user_id' => $id]);
				foreach ($_POST['permission'] as $permission) {
					$this->userPermission->clear()->save(['user_id' => $id, 'permission_id' => $permission]);
				}
			}
			$this->user->refresh_session();

			echo json_encode(['status' => "OK"]);
			//header('Location: ' . $backUrl);
			// die();
		}

		exit;
	}

	public function actionCheckUnique()
	{
		if (!empty($_POST)) {
			$count_id = $this->user->where(['email' => $_POST['email']])->count('id', 'count_id')->getOne()->count_id;
			if ($this->user->clear()->getOne($_POST['id']) && $this->user->clear()->getOne($_POST['id'])->email == $_POST['email']) {
				echo 'true';
			} elseif ($count_id == 0) {
				echo 'true';
			} else {
				echo 'false';
			}
		}
	}

	public function actionModal($parent_id, $id = false)
	{
		$user        = $this->user->factory($id);
		$userParent  = $this->user->factory($parent_id);
		$roles       = $this->role->getAll();
		$permissions = $this->permission->getAll();
		if ($userParent->level > 1) {
			$roles       = $this->role->leftJoin($this->userRole->table, $this->userRole->table . ".role_id", $this->role->table . ".id")->where([$this->userRole->table . ".user_id" => $userParent->id])->getAll();
			$permissions = $this->permission->leftJoin($this->userPermission->table, $this->userPermission->table . ".permission_id", $this->permission->table . ".id")->where([$this->userPermission->table . ".user_id" => $userParent->id])->getAll();
		}
		echo "<div class=\"modal-header\">
        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
        <h4 class=\"modal-title\" id=\"myModalLabel\">Форма пользователя</h4>
      </div>
      <div class=\"modal-body\" id=\"mbody\">" . $this->render('user/form.php',
				[
					'user'        => $user,
					'roles'       => $roles,
					'permissions' => $permissions,
					'parent_id'   => $parent_id,
					'backUrl'     => $_SERVER['HTTP_REFERER'],
					'superadmin'  => 0
				]) . "</div>";
		exit;
	}

	public function actionExportCSV()
	{
		$titles     = array("#", "логин/e-mail", 'создан', 'последняя активность', "блокировка", 'уровень');
		$arr_titles = [];
		foreach ($titles as $item) {
			$arr_titles[] = iconv("utf-8", "windows-1251", $item);
		}
		$titles     = $arr_titles;
		$this->user = USER::current();
		if ($this->user->is(['admin'])) {
			$usersList = $this->user->getAll();
		} else {
			$usersList = $this->user->getChildren();
		}

		$data = [];
		if (!empty($usersList)) {

			foreach ($usersList as $user) {
				$arr    = [];
				$arr[]  = iconv("utf-8", "windows-1251", $user->id);
				$arr[]  = iconv("utf-8", "windows-1251", $user->email);
				$arr[]  = date('H:i:s d.m.Y', strtotime($user->create_at));
				$arr[]  = ($user->login_at != '0000-00-00 00:00:00') ? date('H:i:s d.m.Y', strtotime($user->login_at)) : '-';
				$arr[]  = ($user->blocked == USER::BLOCKED_YES) ? iconv("utf-8", "windows-1251", 'блокирован') : iconv("utf-8", "windows-1251", 'активен');
				$arr[]  = $user->level;
				$data[] = $arr;
			}
		}
		$this->download_send_headers("data_export.csv");
		echo $this->array2csv($data, $titles);
	}

	function download_send_headers($filename)
	{
		// disable caching
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-type: application/csv; charset = Windows-1251");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename={$filename}");
		header("Content-Transfer-Encoding: binary");
	}

	function array2csv(array &$array, $titles)
	{
		if (count($array) == 0) {
			return null;
		}
		ob_start();
		$df = fopen("php://output", 'w');
		fputcsv($df, $titles, ';');
		foreach ($array as $row) {
			fputcsv($df, $row, ';');
		}
		fclose($df);
		return ob_get_clean();
	}

	public function actionTree($id)
	{
		$user = $this->user->getOne(['id' => $id]);
		$node = ($this->user->getChildren()) ? $this->user->getChildren() : array();
		Html::instance()->setCss("/assets/vendors/select2/css/select2.css");
		Html::instance()->setJs("/assets/vendors/select2/js/select2.full.js");
		Html::instance()->setCss("/assets/modules/user/css/userForm.css");
		Html::instance()->setCss("/assets/vendors/jstree/themes/default/style.css");
		Html::instance()->setCss("/assets/modules/user/css/userTree.css");
		Html::instance()->setJs("/assets/vendors/jstree/jstree.min.js");
		Html::instance()->setJs("/assets/vendors/moment-js/moment.js");
		Html::instance()->setJs("/assets/modules/user/js/userRepository.js");
		Html::instance()->setJs("/assets/modules/user/js/userTree.js");
		Html::instance()->setJs("/assets/modules/user/js/user.js");
		Html::instance()->setJs("/assets/modules/user/js/checks.js");
		Html::instance()->title   = 'Список пользователей';
		Html::instance()->content = $this->render('user/tree.php', ['user' => $user, 'node' => $node]);
		Html::instance()->renderTemplate($this->config['main_template'])->show();
	}

	public function actionDelete($id, $ajax = false)
	{
		$userModel    = new USER();
		$user         = $userModel->getOne($id);
		$level        = $userModel->level;
		$level        = intval($level);
		$deleteObject = false;
		if ($level == 2 && $userModel->is('company_admin')) {
			$deleteObject = true;
		}
		$parent = $userModel->getParentsByLevel($level - 1);
		//$children=$this->user->getChildren();

		if ($this->user->deleteAll($id)) {
			$this->userPermission->where(['user_id' => $id])->delete();
			$this->userRole->where(['user_id' => $id])->delete();

			Property_value::instance()->where(['user_id' => $id])->delete();
			if ($ajax) {
				echo "OK";
			} else {
				header('Location: ' . $_SERVER['HTTP_REFERER']);
			}
		};
		exit;
	}

	public function actionBan($id)
	{
		$node_info = $this->user->getOne($id);
		$this->user->clear()->between('left_key', $node_info->left_key, $node_info->right_key)->update(['blocked' => \modules\user\models\USER::BLOCKED_YES]);
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}

	public function actionUnban($id)
	{
		$node_info = $this->user->getOne($id);
		$this->user->clear()->between('left_key', $node_info->left_key, $node_info->right_key)->update(['blocked' => \modules\user\models\USER::BLOCKED_NO]);
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}

	public function actionLoginAs($id)
	{
		$user = $this->user->factory($id);
		$id   = USER::current()->id;
		$this->user->login($user->email, $user->password);
		$_SESSION['user_bak_id'] = $id;
		$href                    = "/user/profile";

		header('Location: ' . $href);
		exit;
	}

	public function actionLoginBack()
	{
		if (empty($_SESSION['user_bak_id'])) {
			throw new \Exception("Error");
		}
		$user = $this->user->factory($_SESSION['user_bak_id']);
		$this->user->login($user->email, $user->password);
		unset($_SESSION['user_bak_id']);
		header('Location: /user/profile');
		exit;
	}

	public function actionDeleteAccount()
	{
		$this->user->getOne(USER::current()->id);
		$this->user->blocked = USER::BLOCKED_YES;
		$this->user->update();
		$this->user->logout();
		echo "OK";
	}

	public function actionGetInfo()
	{
		echo $this->user->factory($_POST['id'])->toSession();
		exit;
	}

	/**
	 * @param Permission $permission
	 */
	private function setPermission(Permission $permission)
	{
		$this->permission = $permission;
	}

	/**
	 * @param Role $role
	 */
	private function setRole(Role $role)
	{
		$this->role = $role;
	}

	/**
	 * @param UserRole $userRole
	 */
	private function setUserRole(UserRole $userRole)
	{
		$this->userRole = $userRole;
	}

	/**
	 * @param USER $user
	 */
	public function setUser(USER $user)
	{
		$this->user = $user;
	}

	/**
	 * @param UserPermission $userPermission
	 */
	private function setUserPermission(UserPermission $userPermission)
	{
		$this->userPermission = $userPermission;
	}


}