<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blend\Tests\Component\DI\Stubs;

use Blend\Tests\Component\DI\Stubs\IBazInterface;

/**
 * Description of Baz
 *
 * @author gevik
 */
class Baz implements IBazInterface {

    public $count;

    public function __construct($count) {
        $this->count = $count;
    }

}