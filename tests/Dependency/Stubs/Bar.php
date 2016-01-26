<?php

namespace Blend\Tests\Dependency\Stubs;

class Bar {

    public $foo;

    public function __construct(Foo $foo) {
        $this->foo = $foo;
    }

}
