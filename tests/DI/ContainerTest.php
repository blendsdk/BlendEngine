<?php

namespace Blend\Tests\DI;

use Blend\Component\DI\Container;
use Blend\Tests\DI\Stubs\Foo;
use Blend\Tests\DI\Stubs\IFoo;
use Blend\Tests\DI\Stubs\IBazInterface;
use Blend\Tests\DI\Stubs\Baz;
use Blend\Tests\DI\Stubs\Bar;

/**
 * Test class for Filesystem
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {

    public function testSimpleClass() {
        $c = new Container();
        $bar = $c->get(Bar::class);
        $this->assertTrue($bar instanceof Bar);
    }

    public function testMissingArgs() {
        $c = new Container();
        $obj = $c->get(Foo::class);
        $this->assertTrue($obj instanceof Foo);
        $this->assertTrue($obj->bar instanceof Bar);
    }

    /**
     * @expectedException \Blend\Component\Exception\InvalidConfigException
     */
    public function testInterfaceOnly() {
        $c = new Container();
        $c->get(IBazInterface::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMissingCallArgument() {
        $c = new Container();
        $c->get(Baz::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithNonExistantArgument() {
        $c = new Container();
        $c->get(Baz::class, ['arg1' => 1]);
    }

    public function testWithBuiltinArgument() {
        $c = new Container();
        $obj = $c->get(Baz::class, ['count' => 10]);
        $this->assertEquals(10, $obj->count);
    }

    public function testWithClassInCallArgument() {
        $bar = new Bar();
        $bar->bar = 'barbar';
        $c = new Container();
        $obj = $c->get(Foo::class, ['bar' => $bar]);
        $this->assertEquals('barbar', $obj->bar->bar);
    }

    public function testWithClassFactory() {
        $c = new Container();
        $c->define(Bar::class, [
            'factory' => function() {
                $bar = new Bar();
                $bar->bar = 'factory';
                return $bar;
            }
        ]);
        $foo = $c->get(Foo::class);
        $this->assertEquals('factory', $foo->bar->bar);
    }

}
