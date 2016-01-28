<?php

namespace Blend\Tests\DependencyInjection;

use Blend\Component\DependencyInjection\Container;
use Blend\Tests\DependencyInjection\Stubs\Foo;
use Blend\Tests\DependencyInjection\Stubs\IFoo;

/**
 * Test class for Filesystem
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {

    public function testDefine() {
        $c = new Container();
        $c->define(IFoo::class, [
            'class' => Foo::class,
            'ar1' => 1,
            'singleton' => true
        ]);
    }

}
