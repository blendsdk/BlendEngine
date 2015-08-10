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

/**
 * Base class for a model object
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Model {

    protected $identifier = 'id';
    protected $table = null;

    protected function getCamlCaseName($name, $prefix) {
        $n = str_replace('_', ' ', $name);
        return $prefix . ucwords($n);
    }

    protected function getSetterName($name) {
        return $this->getCamlCaseName($name, 'set');
    }

    protected function getGetterName($name) {
        return $this->getCamlCaseName($name, 'get');
    }

    protected function hasSetter($name) {
        return method_exists($this, $name);
    }

}
