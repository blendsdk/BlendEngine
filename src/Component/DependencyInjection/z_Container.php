<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\DependencyInjection;

use \ReflectionClass;
use Blend\Component\InvalidConfigException;

/**
 * This class provides dependecy injection container functionality.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Container {

    protected $definitions;

    const KEY_CLASS = 'class';
    const KEY_CALL_PARAMS = 'call-params';
    const KEY_REFLECTION = 'ref';

    public function __construct() {
        $this->definitions = [];
    }

    public function get($interface, $callparams = null) {

        if (is_null($callparams)) {
            $callparams = array();
        }

        if (is_callable($interface)) {
            $interface = call_user_func($interface, $this);
        }

        if (!is_string($interface)) {
            throw new \InvalidArgumentException("Invalid interface name!");
        }

        if (!isset($this->definitions[$interface])) {
            $this->define($interface, $callparams);
        }

        $def = $this->resolveDependencies($interface);
        if (is_callable($callparams)) {
            $callparams = call_user_func($callparams, $this);
        }
        $callparams = array_merge($callparams, call_user_func($def[self::KEY_CALL_PARAMS], $this));
        $callargs = [];
        foreach ($def[self::KEY_REFLECTION] as $argItem) {
            if (isset($callparams[$argItem->name])) {
                $callargs[] = $callparams[$argItem->name];
            } else {
                $type = $argItem->getType();
                $callargs[] = $this->get($type->__toString());
            }
        }
        if (count($def[self::KEY_REFLECTION]) !== 0) {
            return (new ReflectionClass($def[self::KEY_CLASS]))->newInstanceArgs($callargs);
        } else {
            return new $def[self::KEY_CLASS]();
        }
    }

    protected function resolveDependencies($interface) {
        $def = $this->definitions[$interface];
        // resolve
        if (!isset($def[self::KEY_REFLECTION])) {
            $ref = new ReflectionClass($interface);
            $ctor = $ref->getConstructor();
            if ($ctor) {
                $ctorParams = $ref->getConstructor()->getParameters();
                foreach ($ctorParams as $param) {
                    $type = $param->getType();
                    if (!$type->isBuiltin() && !isset($this->definitions[$param->name])) {
                        $this->define($param->name);
                    }
                }
            } else {
                $ctorParams = array();
            }
            $this->definitions[$interface][self::KEY_REFLECTION] = $ctorParams;
            $def = $this->definitions[$interface];
        }
        return $def;
    }

    public function getDefinition($interface) {
        if (isset($this->definitions[$interface])) {
            return $this->definitions[$interface];
        } else {
            return null;
        }
    }

    protected function buildDefinition($interface, $definition = null, $callparams = null) {
        $class = null;
        $params = [];

        if (is_null($callparams)) {
            $callparams = array();
        }

        if ($definition == null) {
            $class = $interface;
        } else if (is_string($definition) || is_callable($definition, true)) {
            $class = $definition;
        } else if (is_array($definition)) {
            if (isset($definition[self::KEY_CLASS])) {
                $class = $definition[self::KEY_CLASS];
                unset($definition[self::KEY_CLASS]);
            } else {
                $class = $interface;
            }
            $params = $definition;
        }

        if (is_array($callparams)) {
            $cp = function(Container $c) use ($params, $callparams) {
                return array_merge($params, $callparams);
            };
        } else if (is_callable($callparams, true)) {
            $cp = function(Container $c) use ($params, $callparams) {
                return array_merge($params, call_user_func($callparams, $c));
            };
        } else {
            throw new InvalidConfigException("Invalid call parameters for $interface!");
        }

        return [
            self::KEY_CLASS => $class,
            self::KEY_CALL_PARAMS => $cp
        ];
    }

    public function define($interface, $definition = null, $callparams = null) {
        if (!isset($this->definitions[$interface])) {
            $this->definitions[$interface] = $this->buildDefinition($interface, $definition, $callparams);
            return $this;
        } else {
            throw new InvalidConfigException("{$interface} is alreay defined in this container!");
        }
    }

}
