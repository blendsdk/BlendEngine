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

/**
 * Description of Counter.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Counter
{
    protected $count;

    public function __construct($start)
    {
        $this->count = $start;
    }

    public function increment()
    {
        return  ++$this->count;
    }
}
