<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Security\Authentication\Methods\Database;

use Blend\Security\UserManagerService as UserManagerServiceBase;
use Blend\Security\Authentication\Methods\Database\SysUser;
use Blend\Database\Database;

class UserManagerService extends UserManagerServiceBase {

    protected $userClass;

    public function __construct(Database $database, $userClass = 'Blend\Security\Authentication\Methods\Database\SysUser') {
        parent::__construct($database);
        $this->userClass = $userClass;
    }

    public function authenticate($token) {
        $stmt = new SelectUserByTokenHashStatement();
        $stmt->setToken($token);
        $result = $this->database->executeStatement($stmt,
                Database::RETURN_FIRST);
        return is_null($result) ? $result : $this->createUserInstance($result);
    }

    protected function createUserInstance($record) {
        return (new \ReflectionClass($this->userClass))->newInstance($record);
    }

}
