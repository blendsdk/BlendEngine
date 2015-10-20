<?php

namespace Blend\Service\UserManager\Database\Service\Base;

use Blend\Database\Database;
use Blend\Database\DatabaseService;
use Blend\Service\UserManager\Database\Model\SysUser;
use Blend\Service\UserManager\Database\Schema\SYS_USER_SCHEMA as SC;

class SysUserService extends DatabaseService {

    protected $recordClass;

    public function __construct(Database $database, $recordClass = null) {
        parent::__construct($database);
        $this->recordClass = is_null($recordClass) ? SysUser::class : $recordClass;
    }

    /**
     * Deletes a sys_user record from the database
     * and returns the deleted record object
     * @return SysUser The record that was deleted
     */
    public function delete(SysUser &$object) {
        $object = $this->deleteObject(SC::TABLE_NAME, $object);
        return $object;
    }

    /**
     * Creates or updates sys_user record
     * and returns the newly or the updated record object
     * @return SysUser The object that was saved
     */
    public function save(SysUser &$object) {
        return $this->saveObject(SC::TABLE_NAME, $object);
    }

    /**
     * @return SysUser
     */
    public function getByUserID($user_id) {
        $params = array(SC::USER_ID => $user_id);
        return $this->getObjectByParams(SC::TABLE_NAME, $params, $this->recordClass);
    }

    /**
     * Deletes a sys_user record from the database
     * and returns the deleted record object
     * @return SysUser The record that was deleted
     */
    public function deleteByUserID($user_id) {
        $params = array(SC::USER_ID => $user_id);
        return $this->deleteByParams(SC::TABLE_NAME, $params, $this->recordClass);
    }

    /**
     * @return SysUser
     */
    public function getByUsername($username) {
        $params = array(SC::USERNAME => $username);
        return $this->getObjectByParams(SC::TABLE_NAME, $params, $this->recordClass);
    }

    /**
     * Deletes a sys_user record from the database
     * and returns the deleted record object
     * @return SysUser The record that was deleted
     */
    public function deleteByUsername($username) {
        $params = array(SC::USERNAME => $username);
        return $this->deleteByParams(SC::TABLE_NAME, $params, $this->recordClass);
    }

    /**
     * @return SysUser
     */
    public function getByUserEmail($user_email) {
        $params = array(SC::USER_EMAIL => $user_email);
        return $this->getObjectByParams(SC::TABLE_NAME, $params, $this->recordClass);
    }

    /**
     * Deletes a sys_user record from the database
     * and returns the deleted record object
     * @return SysUser The record that was deleted
     */
    public function deleteByUserEmail($user_email) {
        $params = array(SC::USER_EMAIL => $user_email);
        return $this->deleteByParams(SC::TABLE_NAME, $params, $this->recordClass);
    }

}
