<?php
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 14:39
 */

namespace modules\user\models;

use core\Model;
use src\dbtree\DbTreeExt;
use modules\user\services\UserPermissions;
use modules\user\services\UserRoles;


/**
 * @property-read Role $roleModel;
 * @property-read Permission $permissionModel;
 * @property-read UserRoles $userRolesService;
 * @property-read UserPermissions $userPermissionsService;
 * Class user
 * @package modules\user\models
 *
 * @property string id - айди
 * @property string email - адрес электронной почты
 * @property string password - пароль
 * @property array roles - роли
 * @property array permissions - допуски
 * @property string login_at - залогинен в
 * @property integer blocked - заблокирован/не заблокирован
 * @property integer left_key - левая граница
 * @property integer right_key - правая граница
 * @property string fio - ФИО
 * @property integer phone_number - номер телефона
 * @property integer level - уровень
 */
class USER extends Model implements \core\interfaces\User
{
	const BLOCKED_NO          = 1;
	const BLOCKED_YES         = 2;
	const MAX_LENGTH_PASSWORD = 6;

	public        $table  = "user";
	public        $errors = [];
	public        $parents;
	public        $master_user_id;
	public        $children;
	public        $rootParent;
	private       $roleModel;
	private       $permissionModel;
	private       $userRolesService;
	private       $userPermissionsService;
	private       $adminActivity;
	public static $instance;
	public $sanitize = ['fio','phone_number'];

	public static $blocked
		= [
			self::BLOCKED_NO  => 'Активен',
			self::BLOCKED_YES => 'Заблокирован',
		];

	public static $classCss
		= [
			self::BLOCKED_NO  => 'success',
			self::BLOCKED_YES => 'default'
		];


	public static function initFirst()
	{
		if (empty($_SESSION['user'])) {
			$u                = new self();
			$_SESSION['user'] = json_encode($u->initUser());
			return true;
		}
		return false;
	}

	public function __construct()
	{
		parent::__construct(false);
		$this->setId('');
		$this->setEmail('');
		$this->setFio('');
		$this->setPhoneNumber(null);
		$this->setToken('');
		$this->setRoles(new Role());
		$this->setPermissions(new Permission());
		$this->setLoginAt('');
		$this->setBlocked('');
		$this->setLeftKey('');
		$this->setRightKey('');
		$this->setLevel('');
		$this->setAdminActivity('');
		$this->parents        = '';
		$this->children       = '';
		$this->rootParent     = '';
		$this->master_user_id = '';

	}

	public static function instance()
	{
		if (static::$instance) {
			return static::$instance;
		} else {

			return new static();
		}
	}

	public static function factory($id = false)
	{
		return self::instance()->build($id);
	}

	private function build($id)
	{
		if ($id == false || $id == 'null' || !$this->getOneWithParams(['id' => $id])) {
			return static::instance();
		}
		return $this;
	}

	public function initUser()
	{
		$obj = new \stdClass();
		$this->setRoles(new Role());
		$this->setPermissions(new Permission());
		$this->setParents();
		$this->setChildren();
		$this->setRootParent();
		$obj->id           = $this->id;
		$obj->roles        = $this->roles;
		$obj->permissions  = $this->permissions;
		$obj->bloked       = USER::BLOCKED_NO;
		$obj->parents      = $this->parents;
		$obj->children     = $this->children;
		$obj->rootParent   = $this->rootParent;
		$obj->fio          = $this->fio;
		$obj->phone_number = $this->phone_number;
		return $obj;
	}


    public function getChildren()
	{

		if ($this->id) {
			$self     = new self();
			$userInfo = $self->getOne($this->id);
			$query    = $self->clear()->select(
				[
					$this->table . ".*"
				])
				->where($this->table . '.left_key', '>', $userInfo->left_key)->where($this->table . '.right_key', '<', $userInfo->right_key);
			return $query->getAll();
		} else {
			return false;
		}

	}

	public function getCountChildren()
	{

		if ($this->id) {
			$self     = new self();
			$userInfo = $self->getOne($this->id);
			$query    = $self->clear()->count(
				'id', 'count_id')
				->where($this->table . '.left_key', '>', $userInfo->left_key)->where($this->table . '.right_key', '<', $userInfo->right_key);
			return $query->getOne()->count_id;
		} else {
			return false;
		}

	}

	public function getChildrenByLevel($level)
	{
		$self = new self();
		return $self->where('left_key', '>', $this->left_key)->where('right_key', '<', $this->right_key)->where(['level', $level])->getAll();
	}

	public function insertNode($parentId, $data = array())
	{
		$node_info = $this->clear()->getOne($parentId);
		if ($parentId) {
			$data = $this->createNode($node_info, $data);
		} else {
			$data = $this->createRootNode($data);
		};
		$this->save($data);
		// echo"<pre>";print_r($this);echo"</pre>";
		$node_id = $this->insertId();
		$this->refresh_session();
		return $node_id;
	}

	private function createRootNode($data)
	{
		$right_node_max    = $this->getRightNodeMax(['level' => 1]);
		$right_id          = $right_node_max->max_right_key;
		$level             = 1;
		$data['left_key']  = $right_id + 1;
		$data['right_key'] = $right_id + 2;
		$data['level']     = $level;
		return $data;
	}

	private function createNode($parent_node, $data)
	{

		$data['left_key']  = $parent_node->right_key;
		$data['right_key'] = $parent_node->right_key + 1;
		$data['level']     = $parent_node->level + 1;

		$sql = 'UPDATE ' . $this->table . ' SET ';
		$sql .= 'left_key=CASE WHEN left_key >' . $parent_node->right_key . ' THEN left_key+2 ELSE left_key END, ';
		$sql .= 'right_key=CASE WHEN right_key>=' . $parent_node->right_key . ' THEN right_key+2 ELSE right_key END ';
		$sql .= 'WHERE right_key>=' . $parent_node->right_key;
		$this->query($sql);

		return $data;
	}

	private function getRightNodeMax($condition)
	{
		return $this->max('right_key', 'max_right_key')->where($condition)->getOne();
	}


	public function getParents()
	{
		if ($this->id) {
			$self = new self();
			return $self->where('left_key', '<=', $this->left_key)->where('right_key', '>=', $this->right_key)->where('level', '<', $this->level)->orderBy('left_key')->getAll();
		} else {
			return false;
		}

	}

	public function getParentsByLevel($level)
	{
		$self   = new self();
		$result = $self->clear()->where('left_key', '<=', $this->left_key)->where('right_key', '>=', $this->right_key)->where(['level' => $level])->orderBy('left_key')->getAll();
		return $result;
	}

	public function deleteAll($nodeId)
	{
		$node_info = $this->getOne($nodeId);
		$left_key  = $node_info->left_key;
		$right_key = $node_info->right_key;
		$delta_id  = (($right_key - $left_key) + 1);
		$this->clear()->where('left_key', '>=', $left_key)->where('right_key', '<=', $right_key)->delete();

		$sql = 'UPDATE ' . $this->table . ' SET ';
		$sql .= 'left_key = CASE WHEN left_key > ' . $left_key . ' THEN left_key - ' . $delta_id . ' ELSE left_key END, ';
		$sql .= 'right_key = CASE WHEN right_key > ' . $left_key . ' THEN right_key - ' . $delta_id . ' ELSE right_key END ';
		$sql .= 'WHERE right_key > ' . $right_key;

		$this->query($sql);

		return true;
	}

	public function getRootParent($user_id)
	{
		if ($user_id) {
			$self      = new self;
			$node_info = $self->getOne($user_id);
			if ($node_info->level > 1) {
				$result = $self->clear()->where('left_key', '<=', $node_info->left_key)->where('right_key', '>=', $node_info->right_key)->where(['level' => 2])->orderBy('left_key')->getOne();
			} else {
				$result = false;
			}
			return $result;
		} else {
			return false;
		}


	}

    /**
     * Восстанавливает данные пользователя из сессии
     * @param $data
     * @return Model|USER
     */
	public static function restore($data){
	    $u = self::instance();
	    foreach($data as $k=>$v){
	        $u->setValue($k,$v);
        }
	    return $u;
    }

    /**
     * Полученный результат данного метоа можно проверять на соотвествие instanceof но нельзя сохранять! типа ->save()
     * @return Model|USER
     */
	public static function current()
	{
		if ($_SESSION['user'] !== null && !is_array($_SESSION['user'])) {
		    $ses=json_decode($_SESSION['user']);
			$result = self::instance(false)->restore($ses);
		} else {
			$result           = self::instance();
			$_SESSION['user'] = json_encode(self::instance()->initUser());
		}
		return $result;

	}

	public static function getLogin()
	{
		$user = self::current();
		return $user->email;
	}


	/**
	 * @alias for isInGroup($params)
	 * @param $params
	 * @return mixed
	 */
	public function is($params)
	{
		return $this->isInGroup($params);
	}

	/**
	 * checks if user is in group/groups
	 * @param $params
	 * @return mixed
	 */
	public function isInGroup($params)
	{
		if (!is_array($this->roles)) {
			return false;
		}
		if (is_array($params)) {
			return (bool)array_intersect($this->roles, $params);
		} else {
			return (bool)in_array($params, $this->roles);
		}
	}

	/**
	 * @alias for hasPermission($params)
	 * @param $params
	 * @return mixed
	 */
	public function can($params)
	{
		return $this->hasPermission($params);
	}

	/**
	 * checks if user has permission/permissions
	 * @param $params
	 * @return mixed
	 */
	public function hasPermission($params)
	{
		if (is_array($params)) {
			return (bool)array_intersect($this->permissions, $params);
		} else {
			return in_array($params, $this->permissions);
		}
	}

	/**
	 * adds group/groups to user
	 * @param $params
	 * @return mixed
	 */
	public function addGroup($params)
	{
		$this->setRoleModel(new Role());
		$this->setUserRolesService(new UserRoles());
		if (is_array($params)) {
			foreach ($params as $param) {
				if ($role = $this->roleModel->clear()->getByName($param)) {
					$this->userRolesService->addUserRole($this->id, $role->id);
					if (!in_array($param, $this->roles))
						@$this->roles[] = $param;
				} else {
					$this->errors[] = 'Ошибка. Такой группы нет в системе';
				}
			}
		} else {
			if ($role = $this->roleModel->getByName($params)) {
				$this->userRolesService->addUserRole($this->id, $role->id);
				if (!in_array($params, $this->roles))
					$this->roles[] = $params;
			} else {
				$this->errors[] = 'Ошибка. Такой группы нет в системе';
			}

		}
	}

	/**
	 * removes user from group/groups
	 * @param $params
	 * @return mixed
	 */
	public function removeGroup($params)
	{
		$this->setUserRolesService(new UserRoles());
		if (is_array($params)) {
			foreach ($params as $param) {
				if ($role = $this->roleModel->getByName($param)) {
					$this->userRolesService->removeUserRole($role->id);
				}
				if (($key = array_search($param, $this->roles)) !== false) {
					unset($this->roles[$key]);
				}
			}
		} else {
			if ($role = $this->roleModel->getByName($params)) {
				$this->userRolesService->removeUserRole($role->id);
			}
			if (($key = array_search($params, $this->roles)) !== false) {

				unset($this->roles[$key]);
			}
		}
	}

	/**
	 * @alias for addRights($params)
	 * @param $params
	 * @return mixed
	 */
	public function addPermissions($params)
	{
		return $this->addRights($params);
	}

	/**
	 * adds right/rights to user
	 * @param $params
	 * @return mixed
	 */
	public function addRights($params)
	{
		$this->setUserPermissionsService(new UserPermissions());
		$this->setPermissionModel(new Permission());
		if (is_array($params)) {
			foreach ($params as $param) {
				if ($permission = $this->permissionModel->clear()->getPermissionByName($param)) {
					$this->userPermissionsService->addUserPermission($permission->id);
					if (!in_array($param, $this->permissions))
						@$this->permissions[] = $param;
				} else {
					$this->errors[] = 'Ошибка. Таких прав нет в системе.';
				}

			}
		} else {
			if ($permission = $this->permissionModel->getPermissionByName($params)) {
				$this->userPermissionsService->addUserPermission($permission->id);
				if (!in_array($params, $this->permissions))

					$this->permissions[] = $params;
			} else {
				$this->errors[] = 'Ошибка. Таких прав нет в системе.';
			}

		}
		return true;

	}

	/**
	 * @alias for removeRights($params)
	 * @param $params
	 * @return mixed
	 */
	public function removePermissions($params)
	{
		return $this->removeRights($params);
	}

	/**
	 * removes right/rights from user
	 * @param $params
	 * @return mixed
	 */
	public function removeRights($params)
	{
		$this->setUserPermissionsService(new UserPermissions());
		if (is_array($params)) {
			foreach ($params AS $param) {
				if ($permission = $this->permissionModel->getPermissionByName($param)) {
					$this->userPermissionsService->removeUserPermission($permission->id);
				}
				if (($key = array_search($param, $this->permissions)) !== FALSE) {
					unset($this->permissions[$key]);
				}
			}
		} else {
			if ($permission = $this->permissionModel->getPermissionByName($params)) {
				$this->userPermissionsService->removeUserPermission($permission->id);
			}
			if (($key = array_search($params, $this->permissions)) !== FALSE) {
				unset($this->permissions[$key]);
			}
		}
	}

	public function beforeSave()
	{
		if (empty($this->phone_number)) {
			$this->phone_number = null;
		}
		$this->rm('roles');
		$this->rm('permissions');
	}

	/**
	 * checks to what level user belongs
	 * @param $level
	 * @return mixed
	 */
	public function isLevel($level)
	{
		$userCurrent = self::current();
		$user        = $this->getOne(['id' => $userCurrent->id]);
		if ($user->level == $level) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * checks if user is admin
	 * @return mixed
	 */
	public function isAdmin()
	{
		return $this->is('admin');
	}

	/**
	 * checks if user is authorizate
	 *
	 * @return mixed
	 */
	public function isAuth()
	{
		return $this->id > 0;
	}


	/**
	 * @alias for stop()
	 * @return mixed
	 */
	public function logout()
	{
		return $this->stop();
	}

	/**
	 * remove user data from $_SESSION , fill $_SESSION default data
	 * @return mixed
	 */
	public function stop()
	{
		session_unset();
		self::initFirst();
		return true;
	}

	/**
	 * authorizes user with $email and $pass, fill $_SESSION with user data from BD
	 * @param $email
	 * @param $pass
	 * @return mixed
	 */
	public function login($email, $pass)
	{
		if ($this->getOneWithParams(['user.email' => $email, 'user.password' => $pass]) && $this->blocked == USER::BLOCKED_NO) {
			$this->clear_session();
			if (!$this->login_at && $this->level <= 2 && $this->is(['company_admin'])) {
				$_SESSION['first_login'] = true;
			}
			$data['id']       = $this->id;
			$data['login_at'] = date('Y-m-d H:i:s');
			if ($this->id == 1) {
				$this->setAdminActivity(['login_at' => $this->login_at, 'view_new_users' => false]);
			}
			$this->setLoginAt($data['login_at']);
			$this->fill_session();
			$this->update($data);
			return true;
		} else {
			return false;
		}

	}

	public function refresh_session()
	{
		$id = json_decode($_SESSION['user'])->id;
		$this->clear_session();
		$this->fill_session($id);
	}

	public function fill_session($id = null)
	{

		if ($id != null) {
			$this->clear()->build($id);
		}
		$_SESSION['user'] = $this->toSession();
	}

	public function clear_session()
	{
		session_unset();
	}

	/**
	 * authorizes admin under user data
	 * @param $id
	 * @return mixed
	 */
	public function authAs($id)
	{
		// TODO: Implement authAs() method.
	}


	public static function isAuthorized()
	{

		$user = self::current();

		if (!empty($user->id) || (intval($user->id) > 0)) {
			return true;
		} else {
			return false;
		}
	}

	public static function hasChildren()
	{
		$self = USER::current();
		if ($self->getChildren()) {
			return true;
		} else {
			return false;
		}
	}

	public function existByParams($params)
	{
		$this->clear()->count('id', 'count_id')->where($params)->getOne();
		if ($this->count_id > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @param mixed $fio
	 */
	public function setFio($fio)
	{
		$this->fio = $fio;
	}

	/**
	 * @param mixed $phone_number
	 */
	public function setPhoneNumber($phone_number)
	{
		$this->phone_number = $phone_number;
	}

	/**
	 * @param mixed $token
	 */
	public function setToken($token)
	{
		$this->token = $token;
	}

	/**
	 * @param mixed $blocked
	 */
	public function setBlocked($blocked)
	{
		$this->blocked = $blocked;
	}

	/**
	 * @param mixed $left_key
	 */
	public function setLeftKey($left_key)
	{
		$this->left_key = $left_key;
	}

	/**
	 * @param mixed $right_key
	 */
	public function setRightKey($right_key)
	{
		$this->right_key = $right_key;
	}

	/**
	 * @param mixed $level
	 */
	public function setLevel($level)
	{
		$this->level = $level;
	}

	/**
	 * @param Role $roles
	 */
	public function setRoles(Role $roles)
	{
		$this->roles = ($roles->getRoles($this->id) && $this->blocked == USER::BLOCKED_NO) ? $roles->getRoles($this->id) : array();
	}

	/**
	 * @param Permission $permissions
	 */
	public function setPermissions(Permission $permissions)
	{
		$this->permissions = ($permissions->getPermissions($this->id) && $this->blocked == USER::BLOCKED_NO) ? $permissions->getPermissions($this->id) : array();
	}

	/**
	 * @param mixed $login_at
	 */
	public function setLoginAt($login_at)
	{
		$this->login_at = $login_at;
	}

	/**
	 * @param Role $roleModel
	 */
	private function setRoleModel(Role $roleModel)
	{
		$this->roleModel = $roleModel;
	}

	/**
	 * @param UserRoles $userRolesService
	 */
	private function setUserRolesService(UserRoles $userRolesService)
	{
		$this->userRolesService = $userRolesService;
	}

	public function generateHashWithSalt($password)
	{
		$salt = substr(sha1($password), 10, 20) . "\3\1\2\6";
		return sha1(sha1($password) . $salt);
	}

	/**
	 * @param UserPermissions $userPermissionsService
	 */
	private function setUserPermissionsService(UserPermissions $userPermissionsService)
	{
		$this->userPermissionsService = $userPermissionsService;
	}

	/**
	 * @param Permission $permissionModel
	 */
	public function setPermissionModel(Permission $permissionModel)
	{
		$this->permissionModel = $permissionModel;
	}

	private function getOneWithParams($condition = false)
	{
		$this->where($condition)->getOne();
		$this->setRoles(new Role());
		$this->setPermissions(new Permission());
		return $this;
	}

	public function toSession()
	{
		$obj = new \stdClass();
		$this->setParents();
		$this->setChildren();
		$this->setRootParent();
		if (isset($this->rootParent->id)) {
			$master_id = $this->rootParent->id;
		} else {
			$master_id = $this->id;
		}
		$obj->id             = $this->id;
		$obj->roles          = $this->roles;
		$obj->permissions    = $this->permissions;
		$obj->email          = $this->email;
		$obj->fio            = $this->fio;
		$obj->phone_number   = $this->phone_number;
		$obj->left_key       = $this->left_key;
		$obj->right_key      = $this->right_key;
		$obj->level          = $this->level;
		$obj->login_at       = $this->login_at;
		$obj->blocked        = $this->blocked;
		$obj->parents        = $this->parents;
		$obj->children       = $this->children;
		$obj->root_parent    = $this->rootParent;
		$obj->master_user_id = $master_id;
		if ($this->adminActivity) {
			$obj->admin_activity = $this->adminActivity;
		}
		return json_encode($obj);
	}

	public function getChildrensUsers()
	{
		$cur_user = self::current();
		return $this->select('id,email')->where('left_key', '>=', $cur_user->left_key)->where('right_key', '<=', $cur_user->right_key)->getAll();
	}

	public function getUsersInMyLevel($getlevel = -1)
	{
		$cur_user = self::current();
		$level    = $cur_user->level;
		$parent   = $this->clear()->where('left_key', '<=', $cur_user->left_key)->where('right_key', '>=', $cur_user->right_key)->where('level', '=', $level + $getlevel)->getOne();
		if ($parent) {
			return $this->clear()->select('id,email')->where('left_key', '>=', $parent->left_key)->where('right_key', '<=', $parent->right_key)->getAll();
		} else {
			return false;
		}
	}

	public function checkMail($email)
	{
		$user = $this->count('id', 'count_id')->where(['email' => $email])->getOne();
		if ($user->count_id > 0 && USER::current()->email != $email) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param mixed $parents
	 */
	public function setParents()
	{
		$parents = $this->getParents();
		$result  = [];
		if ($parents) {
			$roles       = new Role();
			$permissions = new Permission();
			foreach ($parents as $parent) {
				$arr                 = new \stdClass();
				$arr->id             = $parent->id;
				$arr->roles          = $roles->getRoles($parent->id);
				$arr->permissions    = $permissions->getPermissions($parent->id);
				$arr->email          = $parent->email;
				$arr->fio            = $parent->fio;
				$arr->phone_number   = $parent->phone_number;
				$arr->left_key       = $parent->left_key;
				$arr->right_key      = $parent->right_key;
				$arr->level          = $parent->level;
				$arr->login_at       = $parent->login_at;
				$arr->blocked        = $parent->blocked;
				$result[$parent->id] = $arr;
			}

		}
		$this->parents = $result;
	}

	/**
	 * @param string $children
	 */
	public function setChildren()
	{
		$children = $this->getChildren();
		$result   = [];
		if ($children) {
			$roles       = new Role();
			$permissions = new Permission();
			foreach ($children as $child) {
				$arr               = new \stdClass();
				$arr->id           = $child->id;
				$arr->roles        = $roles->getRoles($child->id);
				$arr->permissions  = $permissions->getPermissions($child->id);
				$arr->email        = $child->email;
				$arr->fio          = (isset($child->fio))?$child->fio:"";
				$arr->phone_number = (isset($child->phone_number))?$child->phone_number:"";
				$arr->left_key     = $child->left_key;
				$arr->right_key    = $child->right_key;
				$arr->level        = $child->level;
				$arr->login_at     = $child->login_at;
				$arr->blocked      = $child->blocked;
				$result[]          = $arr;
			}

		}
		$this->children = $children;
	}

	/**
	 * @param mixed $rootParent
	 */
	public function setRootParent()
	{
		$arr = new \stdClass();
		if ($this->id) {
			$rootParent = $this->getRootParent($this->id);

			if ($rootParent) {
				$roles             = new Role();
				$permissions       = new Permission();
				$arr->id           = $rootParent->id;
				$arr->roles        = $roles->getRoles($rootParent->id);
				$arr->permissions  = $permissions->getPermissions($rootParent->id);
				$arr->email        = $rootParent->email;
				$arr->fio          = $rootParent->fio;
				$arr->phone_number = $rootParent->phone_number;
				$arr->left_key     = $rootParent->left_key;
				$arr->right_key    = $rootParent->right_key;
				$arr->level        = $rootParent->level;
				$arr->login_at     = $rootParent->login_at;
				$arr->blocked      = $rootParent->blocked;
			}
		}


		$this->rootParent = $arr;
	}

	/**
	 * @param mixed $adminActivity
	 */
	public function setAdminActivity($adminActivity = [])
	{
		$this->adminActivity = $adminActivity;
	}
}