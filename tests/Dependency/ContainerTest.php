<?php

namespace Blend\Tests\Dependency;

use Blend\Component\Dependency\Container;
use Blend\Tests\Dependency\Stubs\Foo;
use Blend\Tests\Dependency\Stubs\Bar;

/**
 * Test class for Filesystem
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {

    public function testSetter() {
        $c = new Container();
        $obj = $c->get(Foo::class);
    }

}
