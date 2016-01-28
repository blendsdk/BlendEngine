<?php

namespace Blend\Tests\DependencyInjection;

use Blend\Component\DependencyInjection\Container;
use Blend\Tests\DependencyInjection\Stubs\Foo;
use Blend\Tests\DependencyInjection\Stubs\IFoo;
use Blend\Tests\DependencyInjection\Stubs\IBazInterface;
use Blend\Tests\DependencyInjection\Stubs\Baz;

/**
 * Test class for Filesystem
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {

    public function testDefine() {
        $c = new Container();
        $c->define(IBazInterface::class, ['class' => Baz::class, 'count' => 5]);
        $obj = $c->get(Foo::class);
        $obj = null;
//        $c->define(IFoo::class, [
//            'class' => Foo::class,
//            'string' => 'gevik',
//            'singleton' => true
//        ]);
    }

}
