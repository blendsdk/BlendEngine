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

use Blend\Data\DatabaseService;
use Blend\Security\UserManagerService as UserManagerServiceBase;
use Blend\Security\Authentication\Methods\Database\SysUser;

/**
 * Description of UserManagerService
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class UserManagerService extends UserManagerServiceBase {

    protected $userClass;

    public function __construct(\Blend\Data\Database $database, $userClass = 'Blend\Security\Authentication\Methods\Database\SysUser') {
        parent::__construct($database);
        $this->userClass = $userClass;
    }

    public function authenticate($token) {
        $sql = <<<SQL
        select
                *
        from
                sys_user
        where
                md5(username||password) = :hash
                and user_is_active = true
        limit 1
SQL;
        $recordset = $this->database->executeQuery($sql, array(
            ':hash' => $token
        ));
        if (is_array($recordset) && count($recordset) === 1) {
            return $this->createUserInstance($recordset[0]);
        } else {
            return null;
        }
    }

    protected function createUserInstance($record) {
        return (new \ReflectionClass($this->userClass))->newInstance($record);
    }

}
