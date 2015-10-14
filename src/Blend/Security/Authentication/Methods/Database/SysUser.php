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

use Blend\Security\IUser;

/**
 * Description of SysUser
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SysUser implements IUser {

    protected $fields;
    protected $values;

    public function __construct($record = array()) {
        $this->values = array();
        $this->fields = array(
            'user_id' => true,
            'username' => true,
            'user_email' => true,
            'user_isactive' => true,
            'user_date_created' => true,
            'user_date_changed' => true
        );
        foreach ($record as $key => $value) {
            if (isset($this->fields[$key])) {
                $this->values[$key] = $value;
            }
        }
    }

    public function getIdentifier() {
        return $this->values['user_id'];
    }

    public function getUsername() {
        return $this->values['username'];
    }

    public function getEmail() {
        return $this->values['user_email'];
    }

    public function isAuthenticated() {
        return empty($this->values['user_id']) === false;
    }

//put your code here
}
