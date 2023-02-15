<?php
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 30.05.2017
 * Time: 12:41
 */

namespace core\interfaces;


interface User
{
    /**
     * @alias for isInGroup($params)
     * @param $params
     * @return mixed
     */
    public function is($params);

    /**
     * checks if user is in group/groups
     * @param $params
     * @return mixed
     */
    public function isInGroup($params);

    /**
     * @alias for hasPermission($params)
     * @param $params
     * @return mixed
     */
    public function can($params);

    /**
     * checks if user has permission/permissions
     * @param $params
     * @return mixed
     */
    public function hasPermission($params);

    /**
     * adds group/groups to user
     * @param $params
     * @return mixed
     */
    public function addGroup($params);

    /**
     * removes user from group/groups
     * @param $params
     * @return mixed
     */
    public function removeGroup($params);

    /**
     * @alias for addRights($params)
     * @param $params
     * @return mixed
     */
    public function addPermissions($params);

    /**
     * adds right/rights to user
     * @param $params
     * @return mixed
     */
    public function addRights($params);

    /**
     * @alias for removeRights($params)
     * @param $params
     * @return mixed
     */
    public function removePermissions($params);

    /**
     * removes right/rights from user
     * @param $params
     * @return mixed
     */
    public function removeRights($params);

    /**
     * checks to what level user belongs
     * @param $level
     * @return mixed
     */
    public function isLevel($level);

    /**
     * checks if user is admin
     * @return mixed
     */
    public function isAdmin();

    /**
     * resets data from BD
     * @return mixed
     */

    /**
     * @alias for stop()
     * @return mixed
     */
    public function logout();

    /**
     * remove user data from $_SESSION , fill $_SESSION default data
     * @return mixed
     */
    public function stop();

    /**
     * authorizes user with $email and $pass, fill $_SESSION with user data from BD
     * @param $email
     * @param $pass
     * @return mixed
     */
    public function login($email,$pass);

    /**
     * authorizes admin under user data
     * @param $id
     * @return mixed
     */
    public function authAs($id);

}