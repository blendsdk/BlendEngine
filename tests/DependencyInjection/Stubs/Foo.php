<?php

namespace Blend\Tests\DependencyInjection\Stubs;

class Foo {

    /**
     * @param \Blend\Tests\DependencyInjection\Stubs\Bar $bar
     */
    public $bar;

    public function __construct(Bar $bar) {
        $this->bar = $bar;
    }

}
