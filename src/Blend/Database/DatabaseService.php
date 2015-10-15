<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Database;

use Blend\Database\Database;

/**
 * DatabaseSerice is an abstract class that can be used as a Data Access Layer
 * avoifing to use the Database object directly
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DatabaseService {

    const RETURN_ALL = 1;
    const RETURN_FIRST = 2;

    /**
     * @var Database
     */
    protected $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

}
