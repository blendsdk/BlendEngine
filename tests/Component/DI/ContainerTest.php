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
use Blend\Tests\Component\DI\Stubs\Foo;
use Blend\Tests\Component\DI\Stubs\IBazInterface;
use Blend\Tests\Component\DI\Stubs\Baz;
use Blend\Tests\Component\DI\Stubs\Bar;
use Blend\Tests\Component\DI\Stubs\Counter;

/**
 * Test class for ContainerTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {

    public function testObjectAsSingleton() {
        $c = new Container();
        $c->singleton(Counter::class, new Counter());
        $c->get(Counter::class)->next();
        $this->assertEquals(2, $c->get(Counter::class)->next());
    }

    public function testContainSelf() {
        $c = new Container();
        $x = $c->get(Container::class);
        $this->assertEquals($c, $x);
    }

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

    public function testSingleton() {
        $c = new Container();
        $counter1 = $c->singleton('seq', [
            'class' => Counter::class
        ]);
        $counter1 = $c->get('seq');
        $this->assertEquals(1, $counter1->next());
        $counter1->next();

        $another = $c->get('seq');
        $this->assertEquals(3, $another->next());
    }

    public function testCallableArgs() {
        $local = 0;
        $c = new Container();

        $c->define(Baz::class, [
            'count' => function() use(&$local) {
                $local += 1;
                return $local;
            }
        ]);

        $obj1 = $c->get(Baz::class);
        $this->assertEquals(1, $obj1->count);
        $obj2 = $c->get(Baz::class);
        $this->assertEquals(2, $obj2->count);
    }

    public function testSingletonWithFactory() {
        $c = new Container();
        $c->singleton(IBazInterface::class, [
            'factory' => function($count, Container $container) {
                $baz = new Baz($count);
                return $baz;
            }
        ]);
        $baz = $c->get(IBazInterface::class, [
            'count' => 5
        ]);
        $this->assertTrue($baz instanceof Baz);
    }

    public function testClassWithFacoryHavingParameters() {
        $c = new Container();
        $c->define(IBazInterface::class, [
            'factory' => function($count, Container $container) {
                $baz = new Baz($count);
                return $baz;
            }
        ]);
        $baz = $c->get(IBazInterface::class, [
            'count' => 5
        ]);
        $this->assertTrue($baz instanceof Baz);
    }

}
