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
use ReflectionFunction;
use Blend\Component\Exception\InvalidConfigException;

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
    protected function isDefined($interface) {
        return isset($this->classdefs[$interface]);
    }

    /**
     * Extracts information about a given class
     * @param string $classname
     * @return array
     * @throws InvalidConfigException
     */
    private function reflect($classname) {
        $defparams = [];
        $callsig = [];
        if (!is_closure($classname)) {
            $ref = new ReflectionClass($classname);

            if ($ref->isInterface()) {
                throw new InvalidConfigException("$classname is an interface!");
            }

            if ($ctor = $ref->getConstructor()) {
                list($defparams, $callsig) = $this->reflectParameters($ctor);
            }
        } else {
            $ref = new ReflectionFunction($classname);
            list($defparams, $callsig) = $this->reflectParameters($ref);
        }

        return [$defparams, $callsig, $ref];
    }

    private function reflectParameters($ref) {
        $defparams = [];
        $callsig = [];
        if ($ref->getNumberOfParameters() !== 0) {
            foreach ($ref->getParameters() as $param) {
                if ($param->isDefaultValueAvailable()) {
                    $defparams[$param->name] = $param->getDefaultValue();
                }
                $callsig[$param->name] = $param->getClass() ? $param->getClass()->name : null;
            }
        }
        return [$defparams, $callsig];
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
    public function define($interface, array $config = array()) {

        if ($this->isDefined($interface)) {
            throw new InvalidConfigException("$interface already exists in this container!");
        }

        if (is_string($config)) {
            $config = ['class' => $config];
        }

        $classname = $this->extractConfig('class', -1, $interface, $config);
        $singleton = $this->extractConfig('singleton', true, false, $config);
        $factory = $this->extractConfig('factory', -1, null, $config);

        list($defaultparams, $callsig, $refclass) = $this->reflect(is_null($factory) ? $classname : $factory);

        return $this->classdefs[$interface] = [
            'class' => $classname,
            'singleton' => $singleton,
            'callsig' => $callsig,
            'refclass' => $refclass,
            'defparams' => array_merge($defaultparams, $config),
            'factory' => $factory
        ];
    }

    /**
     * This function is used to extract.normalize the configuration argument
     * when defining a class/interface
     * @param string $name
     * @param mixed $return
     * @param mixed $default
     * @param array $config
     * @return mixed
     */
    private function extractConfig($name, $return, $default, &$config) {
        if (isset($config[$name])) {
            $r = $return === -1 ? $config[$name] : $return;
            unset($config[$name]);
            return $r;
        } else {
            return $default;
        }
    }

    /**
     * Retrives an new instance of a given interface. In case of a singleton
     * it returns the same instanse
     * @param string $interface The interface name to instantiate
     * @param array $params The parameters that are used to create the new
     * object
     * @return mixed The newly created object
     */
    public function get($interface, array $params = array()) {

        $singleton = $callsig = $defparams = $refclass = $factory = null;

        if (!$this->isDefined($interface)) {
            extract($this->define($interface));
        } else {
            extract($this->classdefs[$interface]);
        }

        $args = [];
        if (count($callsig) !== 0 && is_bool($singleton)) {
            $callparams = $this->resolve($callsig, array_merge($defparams, $params));
            $args = array_intersect_key($callparams, $callsig);
            $this->checkCallArguments($callsig, $args, $refclass);
        }

        if ($singleton !== false) {
            if ($singleton === true) {
                $this->classdefs[$interface]['singleton'] = $this->newInstanceArgs($refclass, $args, $factory);
            }
            return $this->classdefs[$interface]['singleton'];
        } else {
            return $this->newInstanceArgs($refclass, $args, $factory);
        }
    }

    /**
     * Resolves the call dependencies
     * @param mixed $callsig
     * @param mixed $callparams
     * @return type
     */
    private function resolve($callsig, $callparams) {
        foreach ($callsig as $name => $type) {
            if (!isset($callparams[$name]) && !is_null($type)) {
                $callparams[$name] = $this->get($type);
            }
        }
        return $callparams;
    }

    /**
     * Check if the call arguments are correct in count and name
     * @param type $callsig
     * @param type $args
     * @param type $refclass
     * @throws \InvalidArgumentException
     */
    private function checkCallArguments($callsig, $args, $refclass) {
        $missing = array_diff(array_keys($callsig), array_keys($args));
        $missingCnt = count($missing);
        if ($missingCnt !== 0) {
            $missingArgs = implode(', ', $missing);
            $sigArgs = implode(', ', array_keys($callsig));
            throw new \InvalidArgumentException("Missing {$missingCnt} ($missingArgs) for {$refclass->name}::__construct({$sigArgs})");
        }
    }


    /**
     * Creates a new instance from a ReflectionClass and parameters that was
     * provided from the class definition
     *
     * @param type $refclass
     * @param type $callargs
     * @param type $factory
     * @return type
     */
    private function newInstanceArgs($refclass, $callargs, $factory) {
        $args = [];
        foreach ($callargs as $name => $value) {
            if (is_callable($value)) {
                $args[$name] = call_user_func_array($value, [$this]);
            } else {
                $args[$name] = $value;
            }
        }

        if (is_callable($factory)) {
            return call_user_func_array($factory, [$args, $this]);
        } else {
            if (count($args) === 0) {
                return $refclass->newInstance();
            } else {
                return $refclass->newInstanceArgs($args);
            }
        }
    }

}
