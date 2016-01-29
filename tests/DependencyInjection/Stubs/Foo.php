<?php

namespace Blend\Tests\DependencyInjection\Stubs;

use Blend\Tests\DependencyInjection\Stubs\Bar;
use Blend\Tests\DependencyInjection\Stubs\IBazInterface;

class Foo {

    /**
     * @param \Blend\Tests\DependencyInjection\Stubs\Bar $bar
     */
    public $bar;

    /**
     * @param IBazInterface $baz
     */
    public $baz;

    public function __construct(Bar $bar, IBazInterface $baz) {
        $this->bar = $bar;
        $this->baz = $baz;
    }

}
