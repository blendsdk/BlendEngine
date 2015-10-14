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

use Blend\Service\UserManager\UserManagerService as UserManagerServiceBase;
use Blend\Service\UserManager\Database\SysUser;
use Blend\Database\Database;

class UserManagerService extends UserManagerServiceBase {

    static $USER_CLASS = 'Blend\Service\UserManager\Database\SysUser';

    public function __construct(Database $database) {
        parent::__construct($database);
    }

    public function authenticate($token) {
        $stmt = new SelectUserByTokenHashStatement();
        $stmt->setToken($token);
        $result = $this->database->executeStatement($stmt, Database::RETURN_FIRST);
        return is_null($result) ? $result : $this->createUserInstance($result);
    }

    protected function createUserInstance($record) {
        return (new \ReflectionClass(UserManagerService::$USER_CLASS))->newInstance($record);
    }

}
