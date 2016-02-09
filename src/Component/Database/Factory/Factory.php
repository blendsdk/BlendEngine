<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database\Factory;

use Blend\Component\Database\Database;

/**
 * Factory is the base class for a model factory
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Factory {

    /**
     * Reference to a Database object
     * @var Database 
     */
    protected $database;

    /**
     * Name of the Model Class that is used to convert the database records to
     * @var string 
     */
    protected $modelClass;

    public function __construct(Database $database) {
        $this->database = $database;
    }

}
