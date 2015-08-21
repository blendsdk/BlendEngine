<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Data;

use Blend\Data\Database;
use Blend\Model\Model as ModelBase;

/**
 * Base class for a database model
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Model extends ModelBase {

    protected $identifier = 'id';
    protected $table = null;
    protected $modified = array();
    protected $isNew;

    public function __construct($data = array(), $isNew = true) {
        parent::__construct();
        $this->isNew = $isNew;
    }

    public function setValue($id, $value) {
        if ($this->hasField($id)) {
            $cur = $this->fields[$id][self::KEY_VALUE];
        }
        if (parent::setValue($id, $value)) {
            if ($this->isNew === false) {
                $this->modified[$id] = array($cur, $value);
            }
        }
    }

    public function __set($name, $value) {
        $this->setValue($name, $value);
    }

    public function __get($name) {
        return $this->getValue($name);
    }

    protected function insert(Database $database) {
        $fieldNames = array_keys($this->fields);
        $fieldValues = array();
        foreach ($fieldNames as $name) {
            $fieldValues[":{$name}"] = $this->getValue($name);
        }
        $placeHolders = implode(', ', array_keys($fieldValues));
        $names = implode(', ', $fieldNames);
        $sql = "insert into {$this->table} ($names) values ($placeHolders) returning *";
        $result = $database->executeQuery($sql, $fieldValues);
        $this->isNew = false;
        $this->setValues($result[0]);
    }

    protected function update(Database $database) {
        $placeHolders = array();
        $ids = is_array($this->identifier) ? $this->identifier : array($this->identifier);
        $idClause = array();
        foreach ($ids as $idx => $field) {
            $fieldValues[':identifier' . $idx] = $this->data[$field];
            $idClause[] = "{$field} = :identifier{$idx}";
        }
        foreach ($this->modified as $name => $value) {
            $placeHolders[] = "{$name} = :{$name}";
            $fieldValues[":{$name}"] = $value[1];
        }
        $sets = implode(', ', $placeHolders);
        $idc = implode(' and ', $idClause);
        $sql = "update {$this->table} set {$sets} where $idc returning *";
        $result = $database->executeQuery($sql, $fieldValues);
        $this->modified = array();
        $this->data = $result[0];
    }

    function save(Database $database) {
        if ($this->isNew) {
            $this->insert($database);
        } else {
            $this->update($database);
        }
    }

}
