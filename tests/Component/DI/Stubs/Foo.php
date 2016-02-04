<?php

namespace Blend\Tests\Component\DI\Stubs;

use Blend\Tests\Component\DI\Stubs\Bar;

class Foo {

    /**
     * @param \Blend\Tests\Component\DI\Stubs\Bar $bar
     */
    public $bar;

    public function __construct(Bar $bar) {
        $this->bar = $bar;
    }

}
