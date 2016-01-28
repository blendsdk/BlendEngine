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
    protected $params;

    public function __construct() {
        $this->classdefs = [];
        $this->params = [];
    }

    public function get($interface, $params = array()) {

        if (!$this->isDefined($interface)) {
            $this->define($interface);
        }

        $params = array_merge($this->params[$interface], $params);
    }

    public function isDefined($interface) {
        return isset($this->classdefs[$interface]);
    }

    public function singleton($interface, $config = array()) {
        $this->define($interface, $config);
        $this->classdefs[$interface]['singleton'] = true;
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

        $this->classdefs[$interface] = ['class' => $classname, 'singleton' => $singleton];
        $this->params[$interface] = $config;
    }

}
