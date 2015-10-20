<?php

namespace Blend\Service\UserManager\Database\Service;

use Blend\Database\Database;
use Blend\Service\UserManager\Database\Model\SysUser;
use Blend\Service\UserManager\Database\Schema\SYS_USER_SCHEMA as SC;
use Blend\Service\UserManager\Database\Service\Base\SysUserService as SysUserServiceBase;

class SysUserService extends SysUserServiceBase {

    /**
     * Retrives a user record by token
     * @param string $token
     * @return SysUser
     */
    public function getUserByToken($token) {
        $sql = str_replace_template(
                "select * from @sys_user where md5(@username||@password) = :token", array(
            '@sys_user' => SC::TABLE_NAME,
            '@username' => SC::USERNAME,
            '@password' => SC::PASSWORD
                )
        );
        $params = array(
            ':token' => $token
        );
        $result = $this->database->executeQuery($sql, $params);
        return count($result) === 1 ? (new \ReflectionClass($this->recordClass))->newInstance($result[0]) : null;
    }

}
