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
 * DatabaseFactory.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DatabaseFactory implements ObjectFactoryInterface
{
    protected $DATABASE_CONNECTION_INFO;

    public function __construct($DATABASE_CONNECTION_INFO)
    {
        $this->DATABASE_CONNECTION_INFO = $DATABASE_CONNECTION_INFO;
    }

    public function create()
    {
        return new Database($this->DATABASE_CONNECTION_INFO);
    }
}
