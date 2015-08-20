<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Model;

/**
 * ValidationProvider is a generic class for providing validation
 * and error recording
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ValidationProvider {

    protected $errors;

    /**
     * @abstract function nthat need to be implemented to do the validation
     */
    protected abstract function validate();

    public function __construct() {
        $this->errors = array();
    }

    /**
     * Adds an error message to the errors list
     * @param type $message
     */
    protected function addError($message) {
        $this->errors[] = $message;
    }

    /**
     * Retuns an array of error messages
     * @return string[]
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Initiates validation and retuns of there where any errors
     * @return boolean
     */
    public function isValid() {
        $this->validate();
        return count($this->errors) == 0;
    }

}
