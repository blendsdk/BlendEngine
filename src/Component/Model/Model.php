<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Model;

/**
 * Model represents a data model. It provides functionality to set and get field
 * values and internally keep track of changed fields.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Model
{
    private $data;
    private $updates;
    private $isNew;

    public function __construct($data = array())
    {
        $this->data = $data;
        $this->updates = array();
        $this->isNew = empty($data);
    }

    public function sync($data)
    {
        $this->data = $data;
        $this->updates = array();
        $this->isNew = false;
    }

    /**
     * Indicates if this model is a new Model or hase data from the database.
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * Get the value of the current state of a field, provided that the
     * field exists in the first place, otherwise it will return the
     * default value.
     *
     * @param type $field
     * @param type $default
     *
     * @return mixed
     */
    public function getValue($field, $default = null)
    {
        if (array_key_exists($field, $this->updates)) {
            return $this->updates[$field];
        } elseif (array_key_exists($field, $this->data)) {
            return $this->data[$field];
        } else {
            return $default;
        }
    }

    /**
     * Sets a field value.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blend\Component\Model\Model
     */
    public function setValue($field, $value)
    {
        $this->updates[$field] = $value;

        return $this;
    }

    /**
     * Returns an array with the updated data.
     *
     * @return type
     */
    public function getUpdates()
    {
        return $this->updates;
    }

    /**
     * Returns an array with the initial data that was provided by the
     * constructor.
     *
     * @return type
     */
    public function getInitial()
    {
        return $this->data;
    }

    /**
     * Returns an array with the current state of the record.
     *
     * @return array
     */
    public function getData()
    {
        return array_merge($this->data, $this->updates);
    }

    /**
     * Reruns the JSON version of the getData.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->getData());
    }
}
