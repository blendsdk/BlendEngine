<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\DI\Stubs;

use Blend\Component\DI\ObjectFactoryInterface;

/**
 * Description of CounterFactory.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CounterFactory implements ObjectFactoryInterface
{
    public function create()
    {
        return new Counter(0);
    }
}
