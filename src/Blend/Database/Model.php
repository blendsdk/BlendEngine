<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Database;

use Blend\Database\Database;

/**
 * Base class for a database model
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Model {

    protected $initial;
    protected $values;
    private $unsaved;

    public function __construct(array $record = array()) {
        $this->values = array();
        $this->unsaved = count($record) === 0;
        $this->loadRecord($record);
    }

    /**
     * Provides is this Model is still new (un-saved and just in memory) or it
     * contains a record from the database
     * @return boolean
     */
    public function isUnSaved() {
        return $this->unsaved;
    }

    protected function getValue($field, $default = null) {
        if (isset($this->values[$field])) {
            return $this->values[$field];
        } else {
            return null;
        }
    }

    protected function setValue($field, $value) {
        if (isset($this->initial[$field])) {
            $this->values[$field] = $value;
        }
        return $this;
    }

    public function getInitial() {
        return $this->initial;
    }

    public function getData() {
        return $this->values;
    }

    function loadRecord(array $record = array()) {
        foreach ($record as $field => $value) {
            if (isset($this->initial[$field])) {
                $this->values[$field] = $value;
                $this->initial[$field] = $value;
                $this->unsaved = false;
            }
        }
    }

}
