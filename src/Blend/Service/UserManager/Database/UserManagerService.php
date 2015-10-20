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
use Blend\Service\UserManager\Database\Service\SysUserService;

/**
 * Description of UserManagerService
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class UserManagerService extends UserManagerServiceBase {

    public static $USER_CLASS = 'Blend\Service\UserManager\Database\Model\SysUser';

    public function authenticate($token) {
        $userService = new SysUserService($this->database, self::$USER_CLASS);
        return $userService->getUserByToken($token);
    }

}
