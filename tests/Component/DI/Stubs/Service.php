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
 * Service.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Service
{
    /**
     * @var Database;
     */
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getUsername()
    {
        return $this->database->getUsername();
    }
}
