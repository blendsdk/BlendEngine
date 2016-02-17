<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database\SQL;

/**
 * SQLString is a helper class that can be used to create SQL flavored strings
 * This class provides various function to help cast, wrap, prefix.. etc with
 * a specific sql notation
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SQLString {

    private $str;

    /**
     * Returns a string renderer Closure
     * @return \Closure
     */
    public static function STRING_RENDERER() {
        return function($key, $value, $values) {
            return "E'" . str_replace(array("\'", "\\", "\n", "\r", "\t"), array("''", "\\\\", '\n', '\r', '\t'), $value) . "'";
        };
    }

    public function __construct($str) {
        $this->str = $str;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->str;
    }

    /**
     * Appends a string to the current buffer
     * @param string $str The string to append
     * @param boolean $spaceBefore
     * @param boolean $spaceAfter
     * @return \Blend\Component\Database\SQL\SQLString
     */
    public function append($str, $spaceBefore = false, $spaceAfter = false) {
        $this->str .= ($spaceBefore ? ' ' : '') . $str . ($spaceAfter ? ' ' : '');
        return $this;
    }

    /**
     * Casts a string to a type (aaa::varchar)
     * @param string $type
     * @return \Blend\Component\Database\SQLString
     */
    public function cast($type) {
        $this->str .= '::' . $type;
        return $this;
    }

    /**
     * Prefixes the string with another string (t1.column1)
     * @param string $prefix
     * @return \Blend\Component\Database\SQLString
     */
    public function dotPrefix($prefix) {
        if (!empty($prefix)) {
            $this->str = $prefix . '.' . $this->str;
        }
        return $this;
    }

    /**
     * Helper function to add the column1 AS alias
     * @param string $alias
     * @return \Blend\Component\Database\SQLString
     */
    public function columnAlias($alias) {
        $this->str .= ' AS ' . $alias;
        return $this;
    }

    /**
     * Helper function to ass the table1 t1 alias
     * @param string $alias
     * @return \Blend\Component\Database\SQLString
     */
    public function tableAlias($alias) {
        $this->str .= ' ' . $alias;
        return $this;
    }

    /**
     * Wrapes the string into the md5() function
     * @return \Blend\Component\Database\SQLString
     */
    public function md5() {
        $this->str = "md5({$this->str})";
        return $this;
    }

    /**
     * Adds a SQL concatenation symbol (a || b)
     * @param string $str
     * @return \Blend\Component\Database\SQLString
     */
    public function concat($str) {
        $this->str .= '||' . $str;
        return $this;
    }

    /**
     * Adds the equal sign (a = b)
     * @param string $condition
     * @return \Blend\Component\Database\SQL\SQLString
     */
    public function equalsTo($condition) {
        $this->str .= ' = ' . $condition;
        return $this;
    }

    /**
     * Adds the > sign (a > b)
     * @param string $condition
     * @return \Blend\Component\Database\SQL\SQLString
     */
    public function greaterThan($condition) {
        $this->str .= ' > ' . $condition;
        return $this;
    }

    /**
     * Adds the < sign (a < b)
     * @param string $condition
     * @return \Blend\Component\Database\SQL\SQLString
     */
    public function smallerThan($condition) {
        $this->str .= ' < ' . $condition;
        return $this;
    }

    /**
     * Appends the IS NULL condition
     * @return \Blend\Component\Database\SQL\SQLString
     */
    public function isNull() {
        $this->str .= ' IS NULL';
        return $this;
    }

    /**
     * Appends the IS NOT NULL condition
     * @return \Blend\Component\Database\SQL\SQLString
     */
    public function isNotNull() {
        $this->str .= ' IS NOT NULL';
        return $this;
    }

    /**
     * Wraps the string into a COUNT(...) function
     * @return \Blend\Component\Database\SQL\SQLString
     */
    public function count() {
        $this->str = 'COUNT(' . $this->str . ')';
        return $this;
    }

    /**
     * 
     * @param array $values
     * @return \Blend\Component\Database\SQL\SQLString
     */
    public function notInList(array $values, $itemRenderer = null) {
        $this->str .= ' NOT IN ('
                . implode(', ', $this->renderListItem($values, $itemRenderer)) . ')';
        return $this;
    }

    /**
     * 
     * @param array $values
     * @return \Blend\Component\Database\SQL\SQLString
     */
    public function inList(array $values, $itemRenderer = null) {
        $this->str .= ' IN ('
                . implode(', ', $this->renderListItem($values, $itemRenderer)) . ')';
        return $this;
    }

    /**
     * Renders items of a IN list with a given renderer
     * @param array $values
     * @param Closure $itemRenderer
     * @return array
     */
    private function renderListItem(array $values, $itemRenderer) {
        if (!is_null($itemRenderer)) {
            foreach ($values as $key => $value) {
                $values[$key] = call_user_func_array($itemRenderer, [$key, $value, $values]);
            }
        }
        return $values;
    }

}
