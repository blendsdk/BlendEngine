<?php

namespace Blend\Tests\DI\Stubs;

use Blend\Tests\DI\Stubs\Bar;

class Foo {

    /**
     * @param \Blend\Tests\DI\Stubs\Bar $bar
     */
    public $bar;

    public function __construct(Bar $bar) {
        $this->bar = $bar;
    }

}
