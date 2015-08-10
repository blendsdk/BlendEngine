<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Security;

use Blend\Core\Model;

/**
 * Base User class
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class User extends Model {

    const USERNAME = 'username';
    const PASSWORD = 'password';

    private $securityToken;

    public function __construct() {
        $this->data[self::USERNAME] = 'anonymous';
        $this->data[self::PASSWORD] = null;
    }

    public function getUsername() {
        return $this->data[self::USERNAME];
    }

    public function setSecurityToken($token) {
        $this->securityToken = $token;
    }

    public function isAuthenticated() {
        return !empty($this->securityToken);
    }

}
