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

use Blend\Model\Model;

/**
 * Base User class
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class User extends Model {

    const USERNAME = 'username';

    private $securityToken;

    public function __construct() {
        parent::__construct();
        $this->field(self::USERNAME, 'label.username', self::AUTO_DEFAULT, array(
            $this->validateNotBlank()
        ));
    }

    public function getUsername() {
        return $this->fields[self::USERNAME][self::KEY_VALUE];
    }

    public function setSecurityToken($token) {
        $this->securityToken = $token;
    }

    public function isAuthenticated() {
        return !empty($this->securityToken);
    }

}
