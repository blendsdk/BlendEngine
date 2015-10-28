<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blend\Database;

/**
 * This class is used to return the addition query result values after an
 * executeStatement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class QueryResult {

    private $affectedRecords;

    /**
     * @return integer
     */
    public function getAffectedRecords() {
        return $this->affectedRecords;
    }

    public function populate(\PDOStatement $statement) {
        $this->affectedRecords = $statement->rowCount();
    }

}
