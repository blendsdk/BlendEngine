<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
            'password' => true,
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

    public function isAuthenticated() {
        return empty($this->values['user_id']) === false;
    }

//put your code here
}
