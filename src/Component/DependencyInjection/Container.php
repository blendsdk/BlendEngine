<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blend\Component\DependencyInjection;

use ReflectionClass;
use Blend\Component\InvalidConfigException;

/**
 * Description of Container
 *
 * @author babakhani
 */
class Container {

    protected $classdefs;

    public function __construct() {
        $this->classdefs = [];
    }

    public function isDefined($interface) {
        return isset($this->classdefs[$interface]);
    }

    public function singleton($interface, $config = array()) {
        $this->define($interface, $config);
        $this->classdefs[$interface]['singleton'] = true;
    }

    protected function reflect($classname) {
        $defparams = [];
        $callsig = [];
        $refclass = new \ReflectionClass($classname);
        if ($refclass->isInterface()) {
            throw new InvalidConfigException("$classname is an interface!");
        }

        if ($ctor = $refclass->getConstructor()) {
            if ($ctor->getNumberOfParameters() !== 0) {
                foreach ($ctor->getParameters() as $param) {
                    if ($param->isDefaultValueAvailable()) {
                        $defparams[$param->name] = $param->getDefaultValue();
                    }
                    $callsig[$param->name] = $param->getClass() ? $param->getClass()->name : null;
                }
            }
        }

        return [$defparams, $callsig, $refclass];
    }

    public function define($interface, $config = array()) {

        if ($this->isDefined($interface)) {
            throw new InvalidConfigException("$interface already exists in this container!");
        }

        if (is_string($config)) {
            $config = ['class' => $config];
        }

        $classname = $interface;
        if (isset($config['class'])) {
            $classname = $config['class'];
            unset($config['class']);
        }

        $singleton = false;
        if (isset($config['singleton'])) {
            $singleton = true;
            unset($config['singleton']);
        }

        list($defaultparams, $callsig, $refclass) = $this->reflect($classname);

        $this->classdefs[$interface] = [
            'class' => $classname,
            'singleton' => $singleton,
            'callsig' => $callsig,
            'refclass' => $refclass,
            'defparams' => array_merge($defaultparams, $config)
        ];
    }

    public function get($interface, $params = array()) {

        $class = $singleton = $callsig = $defparams = $refclass = null;

        if (!$this->isDefined($interface)) {
            $this->define($interface, $params);
        }

        extract($this->classdefs[$interface]);

        if (count($callsig) !== 0) {
            $callparams = array_merge($defparams, $params);
            foreach ($callsig as $name => $type) {
                if (!isset($callparams[$name]) && !is_null($type)) {
                    $callparams[$name] = $this->get($type);
                } else {

                }
            }
            $callparams = array_intersect_key($callparams, $callsig);
            return $refclass->newInstanceArgs($callparams);
        } else {
            return $refclass->newInstance();
        }
    }

}
