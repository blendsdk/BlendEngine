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
 * Description of Database.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Database
{
    private $info;

    public function __construct(array $DATABASE_CONNECTION_INFO)
    {
        $this->info = $DATABASE_CONNECTION_INFO;
    }

    public function getUsername()
    {
        return $this->info['username'];
    }
}
