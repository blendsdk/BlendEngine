<?php

namespace Blend\Service\UserManager\Database\Model\Base;

use Blend\Database\Model;
use Blend\Service\UserManager\Database\Schema\SYS_USER_SCHEMA as SC;

abstract class SysUser extends Model {

    public function __construct($record = array()) {
        $this->values = array();
        $this->initial = array(
            SC::USER_ID => true,
            SC::USERNAME => true,
            SC::PASSWORD => true,
            SC::USER_EMAIL => true,
            SC::USER_DATE_CREATED => true,
            SC::USER_DATE_CHANGED => true,
            SC::USER_IS_ACTIVE => true,
        );
        parent::__construct($record);
    }

    /**
     * Getter for the user_id column
     * @param mixed $default defaults to null
     * @return integer
     */
    public function getUserID($default = null) {
        return $this->getValue(SC::USER_ID, $default);
    }

    /**
     * Setter for the user_id column
     * @param mixed $value
     * @return SysUser
     */
    public function setUserID($value) {
        return $this->setValue(SC::USER_ID, $value);
    }

    /**
     * Getter for the username column
     * @param mixed $default defaults to null
     * @return character_varying
     */
    public function getUsername($default = null) {
        return $this->getValue(SC::USERNAME, $default);
    }

    /**
     * Setter for the username column
     * @param mixed $value
     * @return SysUser
     */
    public function setUsername($value) {
        return $this->setValue(SC::USERNAME, $value);
    }

    /**
     * Getter for the password column
     * @param mixed $default defaults to null
     * @return character_varying
     */
    public function getPassword($default = null) {
        return $this->getValue(SC::PASSWORD, $default);
    }

    /**
     * Setter for the password column
     * @param mixed $value
     * @return SysUser
     */
    public function setPassword($value) {
        return $this->setValue(SC::PASSWORD, $value);
    }

    /**
     * Getter for the user_email column
     * @param mixed $default defaults to null
     * @return character_varying
     */
    public function getUserEmail($default = null) {
        return $this->getValue(SC::USER_EMAIL, $default);
    }

    /**
     * Setter for the user_email column
     * @param mixed $value
     * @return SysUser
     */
    public function setUserEmail($value) {
        return $this->setValue(SC::USER_EMAIL, $value);
    }

    /**
     * Getter for the user_date_created column
     * @param mixed $default defaults to null
     * @return timestamp_without_time_zone
     */
    public function getUserDateCreated($default = null) {
        return $this->getValue(SC::USER_DATE_CREATED, $default);
    }

    /**
     * Setter for the user_date_created column
     * @param mixed $value
     * @return SysUser
     */
    public function setUserDateCreated($value) {
        return $this->setValue(SC::USER_DATE_CREATED, $value);
    }

    /**
     * Getter for the user_date_changed column
     * @param mixed $default defaults to null
     * @return timestamp_without_time_zone
     */
    public function getUserDateChanged($default = null) {
        return $this->getValue(SC::USER_DATE_CHANGED, $default);
    }

    /**
     * Setter for the user_date_changed column
     * @param mixed $value
     * @return SysUser
     */
    public function setUserDateChanged($value) {
        return $this->setValue(SC::USER_DATE_CHANGED, $value);
    }

    /**
     * Getter for the user_is_active column
     * @param mixed $default defaults to null
     * @return boolean
     */
    public function getUserIsActive($default = null) {
        return $this->getValue(SC::USER_IS_ACTIVE, $default);
    }

    /**
     * Setter for the user_is_active column
     * @param mixed $value
     * @return SysUser
     */
    public function setUserIsActive($value) {
        return $this->setValue(SC::USER_IS_ACTIVE, $value);
    }

}
