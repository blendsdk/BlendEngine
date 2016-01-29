<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blend\Tests\DI\Stubs;

/**
 * Description of Counter
 *
 * @author babakhani
 */
class Counter {

    private $count;

    public function next() {
        if (is_null($this->count)) {
            $this->count = 0;
        }
        return ( ++$this->count);
    }

}
