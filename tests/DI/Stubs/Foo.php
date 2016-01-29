<?php

namespace Blend\Tests\DI\Stubs;

use Blend\Tests\DI\Stubs\Bar;
use Blend\Tests\DI\Stubs\IBazInterface;

class Foo {

    /**
     * @param \Blend\Tests\DI\Stubs\Bar $bar
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
