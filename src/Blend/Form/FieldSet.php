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

use Blend\Form\Field;
use Blend\Form\ErrorProvider;

/**
 * Description of FieldSet
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class FieldSet extends ErrorProvider {

    protected $name;
    protected $fields;

    public function __construct($name) {
        parent::__construct();
        $this->name = $name;
        $this->fields = array();
    }

    public function getName() {
        return $this->name;
    }

    public function addField($id, Field $field) {
        $this->fields[$id] = $field;
    }

    public function getData() {
        $result = array();
        foreach ($this->fields as $id => $field) {
            $result[$id] = $field->getValue();
        }
        return $result;
    }

    public function setData($values) {
        if (!is_null($values)) {
            foreach ($values as $id => $value) {
                if (isset($this->fields[$id])) {
                    $this->fields[$id]->setValue($value);
                }
            }
        }
    }

    public function validate() {
        foreach ($this->fields as $field) {
            if ($field->validate() === false) {
                $this->errors = array_merge($this->errors, $field->getErrors());
            }
        }
        return $this->isValid();
    }

}
