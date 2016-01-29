<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\DI;

use ReflectionClass;
use Blend\Component\InvalidConfigException;

/**
 * This class implements a constructor-only dependency injection conatiner
 *
 * @author gevikb@gmail.com <Gevik Babakhani>
 */
class Container {

    /**
     * Array of class/interface definitions indexed by the interface name
     * @var array
     */
    protected $classdefs;

    public function __construct() {
        $this->classdefs = [];
    }

    /**
     * Check if an interface is alreay defined
     * @param string $interface
     * @return bool
     */
    public function isDefined($interface) {
        return isset($this->classdefs[$interface]);
    }

    /**
     * Extracts information about a given class
     * @param string $classname
     * @return array
     * @throws InvalidConfigException
     */
    protected function reflect($classname) {
        $defparams = [];
        $callsig = [];
        $refclass = new ReflectionClass($classname);

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

    /**
     * The same at the define(...) method, only defining a class to act as a
     * singleton
     * @param string $interface
     * @param array $config
     */
    public function singleton($interface, $config = array()) {
        $this->define($interface, $config);
        $this->classdefs[$interface]['singleton'] = true;
    }

    /**
     * Defines a class/interface in this container. The interface can also be
     * a class name.
     * @param string $interface The name of the class or interface to be defined
     * in this container. The base way to set this parameter is to use the PHP
     * Class::class nonation
     *
     * @param array $config The configuration parameters for the given interface
     * In case of defining a class by it's interface a key/value pair
     * that is 'class' => 'ClassName' is required. The remaining key/value pairs
     * are going to be used as default call parameters when creating an instanse
     * for this class
     *
     * @throws InvalidConfigException
     */
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

        return $this->classdefs[$interface] = [
            'class' => $classname,
            'singleton' => $singleton,
            'callsig' => $callsig,
            'refclass' => $refclass,
            'defparams' => array_merge($defaultparams, $config)
        ];
    }

    /**
     * Retrives an new instance of a given interface. In case of a singleton
     * it returns the same instanse
     * @param string $interface The interface name to instantiate
     * @param array $params The parameters that are used to create the new
     * object
     * @return object The newly created object
     */
    public function get($interface, $params = array()) {

        $singleton = $callsig = $defparams = $refclass = null;

        if (!$this->isDefined($interface)) {
            extract($this->define($interface, $params));
        } else {
            extract($this->classdefs[$interface]);
        }

        if (count($callsig) !== 0) {
            $callparams = array_merge($defparams, $params);
            foreach ($callsig as $name => $type) {
                if (!isset($callparams[$name]) && !is_null($type)) {
                    $callparams[$name] = $this->get($type);
                } else {

                }
            }
            return $refclass->newInstanceArgs(
                            array_intersect_key($callparams, $callsig)
            );
        } else {
            return $refclass->newInstance();
        }
    }

}
