<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Data;

/**
 * Encapsulates common database functionality. This class is available as
 * a service from the Blend\Core\Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Database extends \PDO {

    public function __construct($config) {
        $dsn = "pgsql:host={$config['host']};dbname={$config['database']};port={$config['port']}";
        parent::__construct($dsn, $config['username'], $config['password']);
    }

    public function executeQuery($sql, $params = array()) {
        $statement = $this->prepare($sql);
        $statement->execute($params);
        if (intval($statement->errorCode()) === 0) {
            return $statement->fetchAll(self::FETCH_ASSOC);
        } else {
            throw DatabaseQueryException::createFromStatement($statement);
        }
    }

}
