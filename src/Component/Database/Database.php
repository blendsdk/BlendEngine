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

use Psr\Log\LoggerInterface;
use Blend\Component\Database\StatementResult;
use Blend\Component\Exception\InvalidConfigException;
use Blend\Component\Exception\DatabaseQueryException;

/**
 * The Database class encapsulates common database operations and connectivity
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Database extends \PDO {

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Retrives the name of the current database
     * @return string
     */
    public function getDatabaseName() {
        return $this->databaseName;
    }

    public function __construct(array $config = array(), LoggerInterface $logger = null) {
        $this->logger = $logger;
        $config = $this->normalizeConfig($config);
        $this->databaseName = $config['database'];
        $dsn = "pgsql:host={$config['host']};dbname={$this->databaseName};port={$config['port']}";
        parent::__construct($dsn, $config['username'], $config['password']);
    }

    /**
     * Executes a SQL statament and returns the first column of the first row in the
     * result set returned by the query. Additional columns or rows are ignored.
     * @param string $sql The SQL to execute
     * @param array $params The parameters to populate the SQL query
     * @return mixed
     */
    public function executeScalar($sql, array $params = array()) {
        $result = $this->executeQuery($sql, $params, null, \PDO::FETCH_NUM);
        if (count($result) !== 0) {
            return $result[0][0];
        } else {
            return null;
        }
    }

    /**
     * Executes a SQL statement and retuns a recordset given resultType
     * @param string $sql The SQL to execute
     * @param array $params The parameters to populate the SQL query
     * @param StatementResult $statementResult The query result to get the number of
     * affected redords
     * @param int $resultType The esult type, defaults to \PDO::FETCH_ASSOC
     * @return mixed
     * @throws DatabaseQueryException
     */
    public function executeQuery($sql, array $params = array(), StatementResult $statementResult = null, $resultType = \PDO::FETCH_ASSOC) {
        $statement = $this->prepare($sql);

        if ($this->logger) {
            $this->logger->debug($sql, $params);
        }

        $statement->execute($params);

        if (intval($statement->errorCode()) === 0) {
            if (!is_null($statementResult)) {
                $statementResult->populate($statement);
            }
            return $statement->fetchAll($resultType);
        } else {
            $exception = DatabaseQueryException::createFromStatement($statement);
            if ($this->logger) {
                $this->logger->error($exception->getMessage(), $params);
            }
            throw $exception;
        }
    }

    /**
     * Executes a multiline SQL query script
     * @param string $sql
     * @return boolean Returns true or exception
     * @throws DatabaseQueryException
     */
    public function executeScript($sql) {

        if (is_array($sql)) {
            $sql = implode(";\n", $sql);
        }

        if ($this->logger) {
            $this->logger->debug($sql);
        }
        $this->exec($sql);
        if (intval($this->errorCode()) !== 0) {
            $exception = DatabaseQueryException::createFromStatement($this);
            if ($this->logger) {
                $this->logger->error($exception->getMessage(), ['sql' => str_replace("\n", ' ', $sql)]);
            }
            throw $exception;
        } else {
            return true;
        }
    }

    /**
     * Normalizes a database connection configuration
     * @param array $config
     * @return array
     * @throws InvalidConfigException
     */
    private function normalizeConfig(array $config) {
        $required = ['username', 'password', 'database'];
        $default = [
            'host' => 'localhost',
            'port' => 5432
        ];
        foreach ($required as $item) {
            if (!array_key_exists($item, $config)) {
                throw new InvalidConfigException("No {$item} configuration was provided!");
            }
        }
        return array_merge($default, $config);
    }

}
