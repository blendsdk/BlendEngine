<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Application\Stubs;

/**
 * Description of GreetingController
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class GreetingController {

    public function hello($name) {
        return 'Hello ' . $name;
    }

    public function index() {
        return "Welcome to " . __CLASS__;
    }

}
