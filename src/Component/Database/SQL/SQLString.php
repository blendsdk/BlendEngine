<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database;

/**
 * SQLString is a helper class that can be used to create SQL flavored strings
 * This class provides various function to help cast, wrap, prefix.. etc with
 * a specific sql notation
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SQLString {

    private $str;

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
     * @param string $type
     * @return \Blend\Component\Database\SQLString
     */
    public function cast($type) {
        $this->str .= '::' . $type;
        return $this;
    }

    /**
     * @param string $prefix
     * @return \Blend\Component\Database\SQLString
     */
    public function dotPrefix($prefix) {
        $this->str = $prefix . '.' . $this->str;
        return $this;
    }

    /**
     * @param string $alias
     * @return \Blend\Component\Database\SQLString
     */
    public function columnAlias($alias) {
        $this->str .= ' as ' . $alias;
        return $this;
    }

    /**
     * @param string $alias
     * @return \Blend\Component\Database\SQLString
     */
    public function tableAlias($alias) {
        $this->str .= ' ' . $alias;
        return $this;
    }

    /**
     * @return \Blend\Component\Database\SQLString
     */
    public function md5() {
        $this->str = "md5({$this->str})";
        return $this;
    }

    /**
     * @param string $str
     * @return \Blend\Component\Database\SQLString
     */
    public function concat($str) {
        $this->str .= '||' . $str;
        return $this;
    }

}
