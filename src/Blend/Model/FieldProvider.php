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

use Blend\Model\ValidationProvider;

/**
 * FieldProvider is a abstract class prividing fields, field values and
 * field attributes to be uased as a base class for a Model
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class FieldProvider extends ValidationProvider {

    const KEY_VALUE = 0;
    const KEY_DEFAULT = 1;
    const KEY_LABEL = 2;
    const KEY_VALIDATORS = 3;
    const KEY_ATTRS = 4;
    const KEY_ERRORS = 5;
    const LABEL_AUTO = null;

    protected $fields;

    public function __construct() {
        parent::__construct();
        $this->fields = array();
    }

    /**
     * Defines a new field
     * @param string $id
     * @param string $label
     * @param string $default
     * @param array $validators
     * @param array $attributes
     */
    protected function field($id, $label = null, $default = null, $validators = array(), $attributes = array()) {
        $this->checkField($id);
        $this->fields[$id] = array(
            self::KEY_VALUE => null,
            self::KEY_DEFAULT => $default,
            self::KEY_LABEL => is_null($label) ? 'label.' . $id : $label,
            self::KEY_VALIDATORS => $validators,
            self::KEY_ATTRS => $attributes,
            self::KEY_ERRORS => array()
        );
    }

    /**
     * Sets (adds) a new validator for a given field
     * @param string $id
     * @param string $validator
     * @param string $call_params
     */
    protected function setValidator($id, $validator, $call_params = array()) {
        $this->checkField($id);
        $this->fields[$id][self::KEY_VALIDATORS][] = array($validator, $call_params);
    }

    /**
     * Sets a new attribute to a given field
     * @param string $id
     * @param string $attr
     * @param string $value
     */
    protected function setAttribute($id, $attr, $value) {
        $this->checkField($id);
        $this->fields[$id][self::KEY_ATTRS][$attr] = $value;
    }

    /**
     * Checks if a field is defined, if so throw an error
     * @param string $id
     * @throws ModelException
     */
    protected function checkField($id) {
        if ($this->hasField($id) === true) {
            throw new ModelException("{$id} field already exists!");
        }
    }

    /**
     * Seta the value of a field
     * @param string $id
     * @param any $value
     */
    public function setValue($id, $value) {
        $setter = $this->getSetterName($id);
        if (method_exists($this, $setter)) {
            call_user_func(array($this, $setter), $value);
            return true;
        } else if ($this->hasField($id)) {
            $this->fields[$id][self::KEY_VALUE] = $value;
            return true;
        }
        return false;
    }

    /**
     * Sets the values of fields based on a key/value collection
     * @param array $values
     * @throws ModelException
     */
    public function setValues($values) {
        if (is_array($values)) {
            foreach ($values as $id => $value) {
                $this->setValue($id, $value);
            }
        } else {
            throw new ModelException('Provided values is not of type Array');
        }
    }

    /**
     * Returns the value of a field or in case of null its default value
     * @param string $id
     * @return any
     */
    public function getValue($id) {
        $getter = $this->getGetterName($id);
        if (method_exists($this, $getter)) {
            $value = call_user_func(array($this, $getter));
        } else if ($this->hasField($id)) {
            $value = $this->fields[$id][self::KEY_VALUE];
        }
        return is_null($value) ? $this->fields[$id][self::KEY_DEFAULT] : $value;
    }

    /**
     * Gets the values of all fields as key/value collection
     * @return type
     */
    public function getValues() {
        $result = array();
        foreach ($this->fields as $id => $field) {
            $result[$id] = $this->getValue($id);
        }
        return $result;
    }

    /**
     * Checks if a field with a given id already exists
     * @param type $id
     * @return type
     */
    protected function hasField($id) {
        return isset($this->fields[$id]);
    }

    /**
     * Retuns the caml case of a given string and the option to
     * att a prefix
     * @param string $name
     * @param string $prefix
     * @return string
     */
    protected function getCamlCaseName($name, $prefix) {
        $n = str_replace('_', ' ', $name);
        return $prefix . ucwords($n);
    }

    /**
     * Creates a caml case stter name
     * @param string $name
     * @return string
     */
    private function getSetterName($name) {
        return $this->getCamlCaseName($name, 'set');
    }

    /**
     * Creates a caml case getter name
     * @param string $name
     * @return string
     */
    private function getGetterName($name) {
        return $this->getCamlCaseName($name, 'get');
    }

    /**
     * Validates all the fields by calling each registered validator
     * on a field.
     */
    protected function validate() {
        $this->errors = array();
        foreach ($this->fields as $id => $field) {
            $validators = $field[self::KEY_VALIDATORS];
            if (is_array($validators)) {
                $this->fields[$id][self::KEY_ERRORS] = array();
                foreach ($validators as $validator) {
                    $error = $this->execValidator($id, $validator[0], $validator[1]);
                    if (is_string($error)) {
                        $this->addError($error);
                        $this->fields[$id][self::KEY_ERRORS][] = $error;
                    }
                }
            }
        }
    }

    /**
     * Execures a validator registered on a field
     * @param type $id
     * @param type $method
     * @param type $args
     * @return type
     * @throws ModelException
     */
    private function execValidator($id, $method, $args) {
        $arguments = array_merge(array(
            $this->getValue($id),
            $this->fields[$id][self::KEY_LABEL],
            $this), $args);
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        } else {
            throw new ModelException("Invalid not non existing validator: $method");
        }
    }

}
