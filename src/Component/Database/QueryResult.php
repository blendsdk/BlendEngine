<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database;

/**
 * The QueryResult is used to return additional query result information
 * after executing the Database->executeStatement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class QueryResult {

    private $affectedRecords;

    /**
     * Get the number od affected records
     * @return integer
     */
    public function getAffectedRecords() {
        return $this->affectedRecords;
    }

    /**
     * Populates this class with the results
     * @param \PDOStatement $statement
     */
    public function populate(\PDOStatement $statement) {
        $this->affectedRecords = $statement->rowCount();
    }

}
