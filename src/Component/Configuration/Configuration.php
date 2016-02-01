<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Configuration;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * The Configuration class Provides an option to read the application
 * configuration parameters In BlendEngine the application configuration
 * is kept in the /config folder based on {environment-name}.php
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Configuration {

    /**
     * Holds the list of paremeters
     * @var \ArrayAccess
     */
    private $params;
    private $level;

    /**
     * Retuns a parameters value of null of the parameter does not exist
     * @param string $name
     * @return object|null
     */
    public function get($name, $default = null) {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        } else {
            return $default;
        }
    }

    /**
     * Checks if the given name exists in this configuration
     * @param string $name
     * @return boolean
     */
    public function has($name) {
        return array_key_exists($name, $this->params);
    }

    public function __construct($fname) {
        $this->level = 0;
        if (file_exists($fname)) {
            $this->params = $this->array_flat(include($fname));
        } else {
            throw new FileNotFoundException($fname, 500);
        }
    }

    protected function array_flat($array, $prefix = '') {
        $result = array();

        foreach ($array as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;

            if (is_array($value) && $this->level < 1) {
                $this->level++;
                $result = array_merge($result, $this->array_flat($value, $new_key));
                $this->level--;
            } else {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }

}
