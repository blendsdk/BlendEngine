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

use Blend\Security\IUser;
use Blend\Database\Schema\SYS_USER_SCHEMA as SC;

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
            SC::USER_ID => true,
            SC::USERNAME => true,
            SC::USER_EMAIL => true,
            SC::USER_IS_ACTIVE => true,
            SC::USER_DATE_CREATED => true,
            SC::USER_DATE_CHANGED => true
        );
        foreach ($record as $key => $value) {
            if (isset($this->fields[$key])) {
                $this->values[$key] = $value;
            }
        }
    }

    public function getIdentifier() {
        return $this->values[SC::USER_ID];
    }

    public function getUsername() {
        return $this->values[SC::USERNAME];
    }

    public function isAuthenticated() {
        return empty($this->values[SC::USER_ID]) === false;
    }

}
