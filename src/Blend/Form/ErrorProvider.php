<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Form;

/**
 * Description of ErrorProvider
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ErrorProvider {

    protected $errors;

    protected abstract function validate();

    public function getErrors() {
        return $this->errors;
    }

    public function __construct() {
        $this->errors = array();
    }

    public function isValid() {
        return count($this->errors) == 0;
    }

}
