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

    /**
     * Retrives objects by their implemented interfaces
     * @param string $interface
     * @return array
     */
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
     * Calls a method on an interface
     * @param string $interface
     * @param string $method
     * @param array $params method parameters
     * @param array $interfaceParams parameters to pass to the class constructor
     * @return mixed
     */
    public function call($interface, $method, array $params = [], array $interfaceParams = []) {
        if (!$this->isDefined($interface)) {
            $this->defineClass($interface);
        }
        list($defaultCallParams, $callSignature, $reflection, $interfaces) = $this->reflect($interface, $method);
        $callArgs = $this->resolveCallParameters($interface, $callSignature, array_merge($defaultCallParams, $params), $method);
        $object = $this->get($interface, $interfaceParams);
        return call_user_func_array([$object, $method], array_values($callArgs));
    }

    /**
     * Checks if a method exists in a ReflectionClass
     * @param ReflectionClass $refclass
     * @param string $method
     * @throws \InvalidArgumentException
     */
    private function assertMethodExists(\ReflectionClass $refclass, $method) {
        if (!$refclass->hasMethod($method)) {
            throw new \InvalidArgumentException(
            "{$refclass->name} does not a method called [$method]");
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
            $callArgs = $this->resolveCallParameters($interface, $callSignature, array_merge($defCtorParams, $defParams, $params));
            $obj = $this->createNewInstance($reflection, $callArgs);
            if ($kind === 's') {
                unset($this->definitions[$interface]);
                $this->setScalar($interface, $obj);
            }
            return $obj;
        }
    }

    /**
     * Resolves call parameters based on input from class definition or
     * a method call
     * @param string $interface
     * @param array $callSignature
     * @param array $params
     * @param string $method
     * @return array
     */
    private function resolveCallParameters($interface, $callSignature, $params, $method = '__construct') {
        $callArgs = [];
        if (!empty($callSignature)) {
            $resolved = $this->resolve($callSignature, $params);
            $callArgs = array_intersect_key($resolved, $callSignature); // clear unwanted params
            $this->assertNotMissingArguments($callSignature, $callArgs, $interface, $method);
        }
        return array_merge($callSignature, $callArgs);
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
    private function reflect($type, $method = null) {
        $ref = new \ReflectionClass($type);
        $defaultCallParams = [];
        $callSignature = [];
        if ($ref->isInterface()) {
            throw new InvalidConfigException(
            "Interface type [$type] cannot be defined in the DI Container!", 1000);
        }

        if ($method === null) {
            $method = $ref->getConstructor();
        } else {
            $this->assertMethodExists($ref, $method);
            $method = $ref->getMethod($method);
        }
        if ($method) {
            list($defaultCallParams, $callSignature) = $this->reflectParameters($method);
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

        /**
         * ReReflect the $reclection. This is needed because PHP fails to
         * serialize ReclectionObjects after deserialization
         */
        $reflection = new ReflectionClass($reflection->name);

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
    private function assertNotMissingArguments($callsig, $args, $name, $methodName) {
        $missing = array_diff(array_keys($callsig), array_keys($args));
        $missingCnt = count($missing);
        if ($missingCnt !== 0) {
            $missingArgs = implode(', ', $missing);
            $sigArgs = implode(', ', array_keys($callsig));
            throw new \InvalidArgumentException("Missing {$missingCnt} ($missingArgs) for {$name}::{$methodName}({$sigArgs})");
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
                    $ptype = is_null($type) ? $name : $type;
                    try {
                        $callParams[$name] = $this->get($ptype);
                    } catch (InvalidConfigException $exc) {
                        /**
                         * If the ptype is an interface and there is no class
                         * defined for it and also there is not default argument
                         * then throw the exception, otherwise we will go further
                         * with that default argument
                         */
                        if ($exc->getCode() === 1000 && !in_array($name, $callParams)) {
                            throw $exc;
                        }
                    }
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
