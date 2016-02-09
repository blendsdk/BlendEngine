<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Schema;

use Blend\DataModelBuilder\Schema\Record;

/**
 * Column represents a column of a table from a PostgreSQL database
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Column extends Record {

    /**
     * Gets the name of this Column
     * @return type
     */
    public function getName() {
        return $this->getString('column_name');
    }

}
