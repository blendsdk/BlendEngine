<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\DI;

use Blend\Component\DI\Container;
use Blend\Tests\Component\DI\Stubs\DummyInterface;
use Blend\Tests\Component\DI\Stubs\ClassWithNoConstructor;
use Blend\Tests\Component\DI\Stubs\ClassWithConstructorParams;
use Blend\Tests\Component\DI\Stubs\ClassWithConstructorAndDefaultParams;
use Blend\Tests\Component\DI\Stubs\Counter;
use Blend\Tests\Component\DI\Stubs\Service;
use Blend\Tests\Component\DI\Stubs\DatabaseFactory;
use Blend\Tests\Component\DI\Stubs\CounterFactory;
use Blend\Tests\Component\DI\Stubs\ClassWithMethods;

class TestContainer extends Container
{

    public function getDefinition($interface)
    {
        return $this->definitions[$interface];
    }

    public function clear()
    {
        $this->definitions = [];
    }
}

interface SomeInterface
{

}

class Class1 implements SomeInterface
{

}

class Class2
{

}

class Class3
{

    public $class2;
    public $someclass;

    public function __construct(Class2 $class2, SomeInterface $someClass = null)
    {
        $this->class2 = $class2;
        $this->someclass = $someClass;
    }
}

/**
 * Test class for ContainerTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{

    public function testNonExistingInterfaceWithCtorDefaultArg() {
        $c = new Container();
        $result = $c->get(Class3::class);
        $this->assertNotNull($result);
    }

    /**
     * @expectedException \Blend\Component\Exception\InvalidConfigException
     */
    public function testDefineInterface() {
        $c = new Container();
        $c->defineClass(DummyInterface::class);
    }

    public function testDefineClassWithNoConstructor() {
        $c = new TestContainer();
        $clazz = ClassWithNoConstructor::class;
        $c->defineClass($clazz);
        list($kind, $type, $params, $defCtorParams, $callSig, $reflection) = array_values($c->getDefinition($clazz));
        $this->assertCount(0, $defCtorParams);
        $this->assertCount(0, $callSig);
    }

    public function testDefineClassConstructorParams()
    {

        $c = new TestContainer();
        $clazz = ClassWithConstructorParams::class;
        $c->defineClass($clazz);
        list($kind, $type, $params, $defCtorParams, $callSig, $reflection) = array_values($c->getDefinition($clazz));
        $this->assertEquals(['param1', 'param2', 'param3'], array_keys($callSig));

        $c->clear();
        $c->defineClass($clazz, ['param2' => 2]);
        list($kind, $type, $params, $defCtorParams, $callSig, $reflection) = array_values($c->getDefinition($clazz));
        $this->assertEquals(['param2' => 2], $params);
    }

    public function testClassWithConstructorAndDefaultParams() {
        $c = new TestContainer();
        $clazz = ClassWithConstructorAndDefaultParams::class;
        $c->defineClass($clazz);
        list($kind, $type, $params, $defCtorParams, $callSig, $reflection) = array_values($c->getDefinition($clazz));
        $this->assertEquals(['param2' => null, 'param3' => []], $defCtorParams);
    }

    public function testGetScalarAndObject() {
        $c = new Container();
        $c->setScalar('database_host', '127.0.0.1');
        $this->assertEquals('127.0.0.1', $c->get('database_host'));
    }

    public function testGetClassWithNoConstructor() {
        $c = new Container();
        $obj = $c->get(ClassWithNoConstructor::class);
        $this->assertInstanceOf(ClassWithNoConstructor::class, $obj);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMissingCtorParameter() {
        $c = new Container();
        $c->get(Counter::class);
    }

    public function testSingleton() {
        $c = new Container();
        $c->defineSingleton(Counter::class, ['start' => 9]);
        $c->get(Counter::class)->increment();
        $this->assertEquals(11, $c->get(Counter::class)->increment());
    }

    public function testGetCorrectDependency() {
        $c = new Container();
        /* @var $service Service */
        $c->setScalar('DATABASE_CONNECTION_INFO', ['username' => 'postgres']);
        $service = $c->get(Service::class);
        $this->assertEquals('postgres', $service->getUsername());
    }

    public function testCreateByFactory() {
        $c = new Container();
        $c->setScalar('DATABASE_CONNECTION_INFO', ['username' => 'postgres']);
        $obj = $c->get(DatabaseFactory::class);
        $this->assertInstanceOf(Stubs\Database::class, $obj);
    }

    public function testCreateSingletonByFactory() {
        $c = new Container();
        $c->defineSingletonWithInterface(Counter::class, CounterFactory::class);

        $o1 = $c->get(Counter::class);
        $o1->increment();

        $o2 = $c->get(Counter::class);
        $this->assertEquals(2, $o2->increment());
    }

    public function testCallMethodWithNoArgs() {
        $c = new Container();
        $result = $c->call(ClassWithMethods::class, 'noArgs');
        $this->assertEquals('noArgs', $result);
    }

    public function testCallWithArgs() {
        $c = new Container();
        $result = $c->call(ClassWithMethods::class, 'withArgs', [
            'arg1' => 'a',
            'arg2' => 'b'
        ]);
        $this->assertEquals('ab', $result);
    }

    public function testCallDefWithArgs() {
        $c = new Container();
        $result = $c->call(ClassWithMethods::class, 'withDefaultArgs', [
            'arg1' => 'a',
            'arg3' => 'b'
        ]);
        $this->assertEquals(['a', 'arg2', 'b'], $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCallWithMissingArgs() {
        $c = new Container();
        $result = $c->call(ClassWithMethods::class, 'withDefaultArgs', [
            'arg2' => 2,
            'arg3' => 3
        ]);
        $this->assertEquals(['a', 'arg2', 'b'], $result);
    }

    public function testReplaceScalarTest() {
        $c = new Container();

        $c->setScalar('_test', 100);
        $this->assertEquals(100, $c->get('_test'));

        $c->setScalar('_test', 200);
        $this->assertEquals(200, $c->get('_test'));
    }

}
