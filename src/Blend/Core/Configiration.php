<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Core;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Provides an option to read the application configuration parameters
 * In BlendEngine the application configuration is kept in the /config
 * folder based on {environment-name}.php
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Configiration {

    /**
     * Holds the list of paremeters
     * @var \ArrayAccess
     */
    private $params;

    /**
     * Retuns a parameters value of null of the parameter does not exist
     * @param string $name
     * @return object|null
     */
    public function get($name) {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        } else {
            return null;
        }
    }

    public function __construct($fname) {
        if (file_exists($fname)) {
            $this->params = include($fname);
        } else {
            throw new FileNotFoundException("Configuration file [{$fname}] does not exist!", 500);
        }
    }

}
