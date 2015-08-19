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

use Blend\Form\ErrorProvider;

/**
 * Base class for a Field in a Form
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Field extends ErrorProvider {

    protected $type;
    protected $value;
    protected $label;
    protected $validators;

    public function __construct($type, $label) {
        parent::__construct();
        $this->type = $type;
        $this->label = $label;
        $this->value = null;
        $this->validators = array();
    }

    private function notBlankValidator() {
        if (empty($this->value)) {
            $this->errors[] = "{$this->label} cannot be empty!";
        }
    }

    /**
     * Adds a notBlank validator to this field
     * @return \Blend\Form\Field
     */
    public function notBlank() {
        $this->validators[] = array('notBlankValidator', array());
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function validate() {
        $this->errors = array();
        foreach ($this->validators as $validator) {
            call_user_func_array(array($this, $validator[0]), $validator[1]);
        }
        return $this->isValid();
    }

}
