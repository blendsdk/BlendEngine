<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database\Schema;

use Blend\Component\Database\Schema\Relation;
use Blend\Component\Database\Schema\Record;

/**
 * Column represents a column of a table from a PostgreSQL database
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Column extends Record {

    const COLUMN_NAME = 'column_name';

    /**
     * Gets the name of this Column
     * @return type
     */
    public function getName() {
        return $this->record[self::COLUMN_NAME];
    }

}
