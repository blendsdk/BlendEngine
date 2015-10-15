<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Service\UserManager\Database;

use Blend\Database\Database;
use Blend\Database\Model\SysUser;
use Blend\Service\UserManager\UserManagerService as UserManagerServiceBase;
use Blend\Service\UserManager\Database\SelectUserByTokenHashStatement;
use Blend\Service\UserManager\Database\SelectUserByEmailStatement;
use Blend\Service\UserManager\Database\CreateNewUserStatement;

class UserManagerService extends UserManagerServiceBase {

    static $USER_CLASS = 'Blend\Database\Model\SysUser';

    public function __construct(Database $database) {
        parent::__construct($database);
    }

    /**
     * Creates a new User
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $is_active
     * @return SysUser
     */
    public function createUser($username, $password, $email, $is_active = true) {
        $stmt = new CreateNewUserStatement();
        $stmt->setUsername($username);
        $stmt->setPassword($password);
        $stmt->setEmail($email);
        $stmt->setIsActive($is_active);
        $result = $this->database->executeStatement($stmt, self::RETURN_FIRST);
        return is_null($result) ? $result : $this->createUserInstance($result);
    }

    public function findUserByEmail($email) {
        $stmt = new SelectUserByEmailStatement();
        $stmt->setEmail($email);
        $result = $this->database->executeStatement($stmt, self::RETURN_FIRST);
        return is_null($result) ? $result : $this->createUserInstance($result);
    }

    public function authenticate($token) {
        $stmt = new SelectUserByTokenHashStatement();
        $stmt->setToken($token);
        $result = $this->database->executeStatement($stmt, self::RETURN_FIRST);
        return is_null($result) ? $result : $this->createUserInstance($result);
    }

    protected function createUserInstance($record) {
        return (new \ReflectionClass(UserManagerService::$USER_CLASS))->newInstance($record);
    }

}
