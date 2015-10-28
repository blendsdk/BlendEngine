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

use Blend\Database\QueryResult;
use Monolog\Logger;

/**
 * Encapsulates common database functionality. This class is available as
 * a service from the Blend\Core\Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Database extends \PDO {

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var boolean;
     */
    private $debug;

    /**
     * @var string
     */
    private $database_name;

    /**
     * @param array $config
     * @param Logger $logger
     * @param boolean $debug
     */
    public function __construct($config, Logger $logger = null, $debug = false) {
        $this->logger = $logger;
        $this->debug = $debug;
        $this->database_name = $config['database'];
        $dsn = "pgsql:host={$config['host']};dbname={$this->database_name};port={$config['port']}";
        parent::__construct($dsn, $config['username'], $config['password']);
    }

    /**
     * Retrives the name of the current database
     * @return string
     */
    public function getDatabaseName() {
        return $this->database_name;
    }

    /**
     * Executes a SQL query script
     * @param type $sql
     * @return boolean
     * @throws DatabaseQueryException
     */
    public function executeScript($sql) {
        $this->debug($sql);
        $result = $this->exec($sql);
        if (intval($this->errorCode()) !== 0) {
            $exception = DatabaseQueryException::createFromStatement($this);
            $this->logError($exception->getMessage());
            throw $exception;
        } else {
            return $result;
        }
    }

    /**
     * Executes a SQL query and returns a recordset as an associative array
     * @param string $sql
     * @param array $params
     * @return array
     * @throws DatabaseQueryException
     */
    public function executeQuery($sql, $params = array(), QueryResult $queryResult = null) {
        $statement = $this->prepare($sql);
        $statement->execute($params);
        $this->debug($sql, $params);

        if (intval($statement->errorCode()) === 0) {
            if(!is_null($queryResult)) {
                $queryResult->populate($statement);
            }
            return $statement->fetchAll(self::FETCH_ASSOC);
        } else {
            $exception = DatabaseQueryException::createFromStatement($statement);
            $this->logError($exception->getMessage(), array(
                'arguments' => $params
            ));
            throw $exception;
        }
    }

    /**
     * Logs a debug message if a logger is provided
     * @param string $message
     * @param array $context
     */
    public function debug($message, $context = array()) {
        if ($this->debug === true && !is_null($this->logger)) {
            $this->logger->debug($message, $context);
        }
    }

    /**
     * Loggs an error message if a logger is provided
     * @param string $message
     * @param array $context
     */
    private function logError($message, $context = array()) {
        if (!is_null($this->logger)) {
            $this->logger->error($message, $context);
        }
    }

}
