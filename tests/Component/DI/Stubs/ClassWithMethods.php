<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\DI\Stubs;

/**
 * Description of ClassMothMethods
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ClassWithMethods {

    public function noArgs() {
        return __FUNCTION__;
    }

    public function withArgs($arg1, $arg2) {
        return $arg1 . $arg2;
    }

    public function withDefaultArgs($arg1, $arg2 = 'arg2', $arg3 = 'arg3') {
        return [$arg1, $arg2, $arg3];
    }

}
