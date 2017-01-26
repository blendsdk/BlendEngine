<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Database;

use Blend\Component\Exception\DatabaseQueryException;
use Blend\Component\Exception\InvalidConfigException;
use Psr\Log\LoggerInterface;

/**
 * The Database class encapsulates common database operations and connectivity.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Database
{
    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $dsn;

    /**
     * Retrieves the name of the current database.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    private function connect()
    {
        if ($this->connection === null) {
            $this->connection = new \PDO($this->dsn, $this->username, $this->password);
        }
    }

    public function __construct(array $config = array(), LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $config = $this->normalizeConfig($config);
        $this->databaseName = $config['database'];
        $this->dsn = "pgsql:host={$config['host']};dbname={$this->databaseName};port={$config['port']}";
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    /**
     * Executes a SQL statement and returns the first column of the first row in the
     * result set returned by the query. Additional columns or rows are ignored.
     *
     * @param string $sql    The SQL to execute
     * @param array  $params The parameters to populate the SQL query
     *
     * @return mixed
     */
    public function executeScalar($sql, array $params = array())
    {
        $result = $this->executeQuery($sql, $params, null, \PDO::FETCH_NUM);
        if (count($result) !== 0) {
            return $result[0][0];
        } else {
            return null;
        }
    }

    /**
     * Executes a SQL statement and returns a recordset given resultType.
     *
     * @param string          $sql             The SQL to execute
     * @param array           $params          The parameters to populate the SQL query
     * @param StatementResult $statementResult The query result to get the number of
     *                                         affected records
     * @param int             $resultType      The result type, defaults to \PDO::FETCH_ASSOC
     *
     * @return mixed
     *
     * @throws DatabaseQueryException
     */
    public function executeQuery($sql, array $params = array(), StatementResult $statementResult = null, $resultType = \PDO::FETCH_ASSOC)
    {
        $this->connect();
        $statement = $this->connection->prepare($sql);

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
     * Executes a multi-line SQL query script.
     *
     * @param string $sql
     *
     * @return bool Returns true or exception
     *
     * @throws DatabaseQueryException
     */
    public function executeScript($sql)
    {
        if (is_array($sql)) {
            $sql = implode(";\n", $sql);
        }

        if ($this->logger) {
            $this->logger->debug($sql);
        }
        $this->connect();
        $this->connection->exec($sql);
        if (intval($this->connection->errorCode()) !== 0) {
            $exception = DatabaseQueryException::createFromStatement($this->connection);
            if ($this->logger) {
                $this->logger->error($exception->getMessage(), array('sql' => str_replace("\n", ' ', $sql)));
            }
            throw $exception;
        } else {
            return true;
        }
    }

    /**
     * Inserts a record into the database using an associative
     * array (key => value pairs).
     *
     * @param string          $table_name      The name of the table to insert
     * @param array           $params          An associative array containing key => value pairs
     * @param StatementResult $statementResult The StatementResult
     * @param number          $resultType      The PDO Fetch type
     *
     * @return array
     */
    public function insert($table_name, array $params, StatementResult &$statementResult = null, $resultType = \PDO::FETCH_ASSOC)
    {
        $keys = str_repeat('?, ', count($params) - 1).'?';
        $sql = 'INSERT INTO '
                .$table_name
                .' ('.implode(', ', array_keys($params))
                .') VALUES ('.$keys.') RETURNING *';

        return $this->executeQuery($sql, array_values($params), $statementResult, $resultType);
    }

    /**
     * Deletes a record from the database using a custom condition and
     * an associative array as condition parameters.
     *
     * @param string          $table_name      The name of the table
     * @param string          $condition       The WHERE condition
     * @param mixed           $cparams         The condition parameters
     * @param StatementResult $statementResult The StatementResult
     * @param number          $resultType      The PDO Fetch type
     *
     * @return array The deleted record
     */
    public function delete($table_name, $condition, $cparams, StatementResult &$statementResult = null, $resultType = \PDO::FETCH_ASSOC)
    {
        if (empty($condition)) {
            throw new \InvalidArgumentException(
            'The delete statement needs a condition to operate correctly. '.
            'If you want to delete all the records '.
            "from {$table_name} then use the truncate(...) method.");
        }

        $sql = 'DELETE FROM '
                .$table_name
                .' WHERE '
                .$condition
                .' RETURNING *';

        return $this->executeQuery($sql, $cparams, $statementResult, $resultType);
    }

    /**
     * Updates a record from the database using associative arrays as column
     * setters.
     *
     * @param string          $table_name      the name of the table to update
     * @param array           $params          column setters. This is an associative array
     * @param string          $condition       The WHERE clause
     * @param array           $cparams         The WHERE clause parameters
     * @param StatementResult $statementResult The StatementResult
     * @param int             $resultType      The PDO Fetch type
     *
     * @return array The updated record result
     *
     * @throws \InvalidArgumentException
     */
    public function update($table_name, array $params, $condition, array $cparams, StatementResult &$statementResult = null, $resultType = \PDO::FETCH_ASSOC)
    {
        $setkeys = array();
        $setparams = array();
        foreach ($params as $field => $value) {
            $param = ':sp_'.$field;
            $setkeys[] = $field.'='.$param;
            $setparams[$param] = $value;
        }

        if (empty($condition)) {
            throw new \InvalidArgumentException(
            'The update statement needs a condition to operate correctly. ');
        }

        if (empty($params)) {
            throw new \InvalidArgumentException(
            'Unable to determine which column to update! '.
            'The $params argument contains no data (the array is empty)!'
            );
        }

        $sql = 'UPDATE '
                .$table_name
                .' SET '.implode(', ', $setkeys)
                .' WHERE '.$condition.' RETURNING *';

        return $this->executeQuery($sql, array_merge($setparams, $cparams), $statementResult, $resultType);
    }

    /**
     * Normalizes a database connection configuration.
     *
     * @param array $config
     *
     * @return array
     *
     * @throws InvalidConfigException
     */
    private function normalizeConfig(array $config)
    {
        $required = array('username', 'password', 'database');
        $default = array(
            'host' => 'localhost',
            'port' => 5432,
        );
        foreach ($required as $item) {
            if (!array_key_exists($item, $config)) {
                throw new InvalidConfigException("No {$item} configuration was provided!");
            }
        }

        return array_merge($default, $config);
    }
}
