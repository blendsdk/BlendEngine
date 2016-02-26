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

use \ReflectionClass;
use Blend\Component\DI\ObjectFactoryInterface;
use Blend\Component\Exception\InvalidConfigException;

/**
 * Conatiner provides a basic constructor based Dependecy Injection Container
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Container {

    /**
     * @var array
     */
    protected $definitions;
    protected $byInterfaceIndex;

    public function __construct() {
        $this->definitions = [];
        $this->byInterfaceIndex = [];
    }

    /**
     * Checks to see if an interface is already defined
     * @param string $interface
     * @return boolean
     */
    public function isDefined($interface) {
        return array_key_exists($interface, $this->definitions);
    }

    public function getByInterface($interface) {
        if (isset($this->byInterfaceIndex[$interface])) {
            $result = [];
            foreach ($this->byInterfaceIndex[$interface] as $item) {
                $result[] = $this->get($item);
            }
            return $result;
        } else {
            return [];
        }
    }

    /**
     * Gets an object from the container using an interface name. If the object
     * by the given interface name does not exist, the container will
     * automatically define the interface as class (defineClass(....)) and then
     * it returns a new instance of that class
     * @param string $interface
     * @param array $params
     * @return mixed
     */
    public function get($interface, array $params = []) {
        if (!$this->isDefined($interface)) {
            $this->defineClass($interface, $params);
        }
        list($kind, $type, $defParams, $defCtorParams, $callSignature, $reflection) = array_values($this->definitions[$interface]);
        if ($kind === 'x') {
            return $defParams[0];
        } else {
            $callArgs = [];
            if (!empty($callSignature)) {
                $resolved = $this->resolve($callSignature, array_merge($defCtorParams, $defParams, $params));
                $callArgs = array_intersect_key($resolved, $callSignature); // clear unwanted params
                $this->assertNotMissingArguments($callSignature, $callArgs, $interface);
            }
            // array_merge here will sort the call params to the correct sort order
            $obj = $this->createNewInstance($reflection, array_merge($callSignature, $callArgs));
            if ($kind === 's') {
                unset($this->definitions[$interface]);
                $this->setScalar($interface, $obj);
            }
            return $obj;
        }
    }

    /**
     * Sets a list of scalers into the conatiner
     * @param array $scalars
     * @throws InvalidConfigException
     */
    public function setScalars(array $scalars) {
        if (!is_array_assoc($scalars)) {
            throw new InvalidConfigException('The \$scalars argument must be an associatibe array!');
        }
        foreach ($scalars as $name => $value) {
            $this->setScalar($name, $value);
        }
    }

    /**
     * Sets a scalar into the container
     * @param string $name
     * @param mixed $value
     */
    public function setScalar($name, $value) {
        $this->define($name, [
            'kind' => 'x',
            'type' => gettype($value),
            'params' => [$value]
        ]);
    }

    /**
     * Defines a singleton with an interface and creation paramaters
     * @param string $interface
     * @param string $className
     * @param array $params
     */
    public function defineSingletonWithInterface($interface, $className, array $params = []) {
        $this->define($interface, [
            'kind' => 's',
            'type' => $className,
            'params' => $params
        ]);
    }

    /**
     * Defines a singleton with creation paramaters
     * @param string $className
     * @param array $params
     */
    public function defineSingleton($className, array $params = []) {
        $this->define($className, [
            'kind' => 's',
            'type' => $className,
            'params' => $params
        ]);
    }

    /**
     * Defines a class with an interface and creation paramaters
     * @param string $interface
     * @param string $className
     * @param array $params
     */
    public function defineClassWithInterface($interface, $className, array $params = []) {
        $this->define($interface, [
            'kind' => 'c',
            'type' => $className,
            'params' => $params
        ]);
    }

    /**
     * Defines a class with creation paramaters
     * @param type $className
     * @param array $params
     */
    public function defineClass($className, array $params = []) {
        $this->define($className, [
            'kind' => 'c',
            'type' => $className,
            'params' => $params
        ]);
    }

    /**
     * Defines an interface. This function is called by other define... methods
     * @param type $interface
     * @param array $data
     */
    private function define($interface, array $data = []) {
        $this->assertNotExists($interface);
        list($kind, $type, $params) = array_values($data);
        if ($kind === 'c' || $kind === 's') {
            list($defaultCallParams, $callSignature, $reflection, $interfaces) = $this->reflect($type);
            $this->definitions[$interface] = array_merge($data, [
                'defCtorParams' => $defaultCallParams,
                'callSignature' => $callSignature,
                'reflection' => $reflection,
            ]);
            foreach ($interfaces as $item) {
                if (!isset($this->byInterfaceIndex)) {
                    $this->byInterfaceIndex[$item] = [];
                }
                $this->byInterfaceIndex[$item][] = $interface;
            }
        } else {
            $this->definitions[$interface] = array_merge($data, [
                'defCtorParams' => [],
                'callSignature' => [],
                'reflection' => null,
            ]);
        }
    }

    /**
     * Reflects a type to get the constructor and its call arguments
     * @param string $type
     * @return mixed
     * @throws InvalidConfigException
     */
    private function reflect($type) {
        $ref = new \ReflectionClass($type);
        $defaultCallParams = [];
        $callSignature = [];
        $constructor = null;
        if ($ref->isInterface()) {
            throw new InvalidConfigException(
            "Interface type [$type] cannot be defined in the DI Container!");
        }

        if ($constructor = $ref->getConstructor()) {
            list($defaultCallParams, $callSignature) = $this->reflectParameters($constructor);
        }

        return [$defaultCallParams, $callSignature, $ref, $ref->getInterfaceNames()];
    }

    /**
     * Reflects a \ReflectionFunction
     * @param \ReflectionFunction $ref
     * @return mixed
     */
    private function reflectParameters($ref) {
        $defaultParameters = [];
        $callSignature = [];
        if ($ref->getNumberOfParameters() !== 0) {
            foreach ($ref->getParameters() as $param) {
                if ($param->isDefaultValueAvailable()) {
                    $defaultParameters[$param->name] = $param->getDefaultValue();
                }
                $callSignature[$param->name] = $param->getClass() ? $param->getClass()->name : null;
            }
        }
        return [$defaultParameters, $callSignature];
    }

    /**
     * Creates a new instance of a reflection type
     * @param ReflectionClass $reflection
     * @param array $callArgs
     * @return mixed
     * @throws InvalidConfigException
     */
    private function createNewInstance(\ReflectionClass $reflection, array $callArgs) {

        if (empty($callArgs)) {
            $instance = $reflection->newInstance();
        } else {
            $instance = $reflection->newInstanceArgs($callArgs);
        }

        if ($reflection->implementsInterface(ObjectFactoryInterface::class)) {
            $instance = call_user_func([$instance, 'create']);
            if ($instance === null) {
                throw new InvalidConfigException($reflection->getName() . '->create() did not reaturn an object instance');
            }
        }
        return $instance;
    }

    /**
     * Check to see if the interface alreay exists, in which case it throws
     * an InvalidConfigException
     * @param string $interface
     * @throws InvalidConfigException
     */
    private function assertNotExists($interface) {
        if ($this->isDefined($interface)) {
            throw new InvalidConfigException("$interface already exists in this container!");
        }
    }

    /**
     * Checks if the privided args matches the goven call signature
     * @param array $callsig
     * @param array $args
     * @param string $name
     * @throws \InvalidArgumentException
     */
    private function assertNotMissingArguments($callsig, $args, $name) {
        $missing = array_diff(array_keys($callsig), array_keys($args));
        $missingCnt = count($missing);
        if ($missingCnt !== 0) {
            $missingArgs = implode(', ', $missing);
            $sigArgs = implode(', ', array_keys($callsig));
            throw new \InvalidArgumentException("Missing {$missingCnt} ($missingArgs) for {$name}::__construct({$sigArgs})");
        }
    }

    /**
     * Resolves the dependencies of a call signature
     * @param array $callSignature
     * @param array $callParams
     * @return array
     */
    private function resolve(array $callSignature, array $callParams) {
        foreach ($callSignature as $name => $type) {
            if (!isset($callParams[$name])) {
                if ($this->isDefined($name) || $this->isDefined($type) || !$this->isBuiltInType($type)) {
                    $callParams[$name] = $this->get(is_null($type) ? $name : $type);
                }
            }
        }
        return $callParams;
    }

    /**
     * Check if the given type is a built-in PHP type
     *
     * @param type $type
     * @return type
     */
    private function isBuiltInType($type) {
        /**
         * @todo Update for PHP7
         */
        return is_null($type);
    }

}
