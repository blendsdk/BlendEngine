<?php

namespace Blend\Database\Model\Base;

use Blend\Database\Schema\SYS_USER_SCHEMA as SC;

class SysUser {

    protected $fields;
    protected $values;

    public function __construct($record = array()) {
        $this->values = array();
        $this->fields = array(
            SC::USER_ID => true,
            SC::USERNAME => true,
            SC::PASSWORD => true,
            SC::USER_EMAIL => true,
            SC::USER_DATE_CREATED => true,
            SC::USER_DATE_CHANGED => true,
            SC::USER_IS_ACTIVE => true,
        );
        foreach ($record as $key => $value) {
            if (isset($this->fields[$key])) {
                $this->values[$key] = $value;
            }
        }
    }

    /**
     * Getter for the user_id column
     * @param mixed $default, defaults to null
     * @return integer
     */
    public function getUserId($default = null) {
        return isset($this->values[SC::USER_ID]) ? $this->values[SC::USER_ID] : $default;
    }

    /**
     * Getter for the username column
     * @param mixed $default, defaults to null
     * @return character_varying
     */
    public function getUsername($default = null) {
        return isset($this->values[SC::USERNAME]) ? $this->values[SC::USERNAME] : $default;
    }

    /**
     * Getter for the password column
     * @param mixed $default, defaults to null
     * @return character_varying
     */
    public function getPassword($default = null) {
        return isset($this->values[SC::PASSWORD]) ? $this->values[SC::PASSWORD] : $default;
    }

    /**
     * Getter for the user_email column
     * @param mixed $default, defaults to null
     * @return character_varying
     */
    public function getUserEmail($default = null) {
        return isset($this->values[SC::USER_EMAIL]) ? $this->values[SC::USER_EMAIL] : $default;
    }

    /**
     * Getter for the user_date_created column
     * @param mixed $default, defaults to null
     * @return timestamp_without_time_zone
     */
    public function getUserDateCreated($default = null) {
        return isset($this->values[SC::USER_DATE_CREATED]) ? $this->values[SC::USER_DATE_CREATED] : $default;
    }

    /**
     * Getter for the user_date_changed column
     * @param mixed $default, defaults to null
     * @return timestamp_without_time_zone
     */
    public function getUserDateChanged($default = null) {
        return isset($this->values[SC::USER_DATE_CHANGED]) ? $this->values[SC::USER_DATE_CHANGED] : $default;
    }

    /**
     * Getter for the user_is_active column
     * @param mixed $default, defaults to null
     * @return boolean
     */
    public function getUserIsActive($default = null) {
        return isset($this->values[SC::USER_IS_ACTIVE]) ? $this->values[SC::USER_IS_ACTIVE] : $default;
    }

}
