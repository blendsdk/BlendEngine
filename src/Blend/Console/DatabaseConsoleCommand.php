<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Console;

use Blend\Database\Database;
use Blend\Database\DatabaseQueryException;
use Blend\Console\ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for a Console command that need to do operations on the database
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class DatabaseConsoleCommand extends ConsoleCommand {

    /**
     * @var Database;
     */
    protected $database;

    protected abstract function executeDatabaseOperation(InputInterface $input, OutputInterface $output);

    protected function getConstraintsColumns($constraint) {
        $sql = <<<SQL
            select
                    *
            from
                    information_schema.constraint_column_usage
            where
                    table_catalog = :database and
                    table_name = :table and
                    constraint_name = :name and
                    table_schema='public'
SQL;

        $columns = $this->database->executeQuery(
                $sql, array(
            ':database' => $this->database->getDatabaseName(),
            ':table' => $constraint['table_name'],
            ':name' => $constraint['constraint_name']
        ));

        $result = array();
        foreach ($columns as $col) {
            $result[] = $col['column_name'];
        }
        return $result;
    }

    protected function getTableConstraints($table_table) {
        $sql = <<<SQL
            select
                    *
            from
                    information_schema.constraint_table_usage
            where
                    table_catalog = :database and
                    table_name = :table and
                    table_schema='public'
SQL;
        $rset = $this->database->executeQuery(
                $sql, array(
            ':database' => $this->database->getDatabaseName(),
            ':table' => $table_table
        ));

        $result = array();

        foreach ($rset as $idx => $rec) {
            $name = $rec['constraint_name'];
            $type = 'key';
            if (stripos($name, '_pkey') !== false) {
                $type = 'primary';
            } else if (stripos($name, '_fkey') !== false) {
                $type = 'foreign';
            }
            $rec['columns'] = $this->getConstraintsColumns($rec);
            $result[$type][] = $rec;
        }
        return $result;
    }

    protected function getTables($search) {
        $sql = <<<SQL
            select
                    *
            from
                    information_schema.tables
            where
                    table_name like :table and
                    table_catalog = :database and
                    table_schema='public'
SQL;

        $rset =  $this->database->executeQuery(
                        $sql, array(
                    ':database' => $this->database->getDatabaseName(),
                    ':table' => $search
        ));

        foreach($rset as $idx => $rec) {
            $constraints = $this->getTableConstraints($rec['table_name']);
            if(isset($constraints['primary'])) {
                $rec['primary'] = $constraints['primary'][0]['columns'];
            } else {
                $rec['primary'] = array();
            }
            $rset[$idx] = $rec;
        }

        return $rset;
    }

    protected function getTableColumns($table_name) {
        $sql = <<<SQL
            select
                    *
            from
                    information_schema.columns
            where
                            table_catalog = '{$this->database->getDatabaseName()}' and
                            table_schema = 'public' and
                            table_name  = '{$table_name}'
SQL;

        $columns = $this->database->executeQuery($sql);
        foreach ($columns as $index => $column) {
            $column['description'] = 'Column is '
                    . ($column['is_nullable'] ? 'Nullable' : 'Not Nullable')
                    . '. Defaults to '
                    . (empty($column['column_default']) ? 'NULL' : $column['column_default']);
            $column['data_type'] = str_replace(' ', '_', $column['data_type']);
            $column['column_name_upr'] = strtoupper($column['column_name']);
            $column['column_name_getter_name'] = $this->ucWords($column['column_name'], 'get');
            $column['column_name_setter_name'] = $this->ucWords($column['column_name'], 'set');
            $columns[$index] = $column;
        }
        return $columns;
    }

    protected function executeInternal(InputInterface $input, OutputInterface $output) {
        if ($this->initDatabaseConnection()) {
            $this->executeDatabaseOperation($input, $output);
        }
    }

    /**
     * Truncated all the tables in the Database
     */
    protected function truncateAllTables() {
        $tables = $this->database->executeQuery("select * from information_schema.tables where table_catalog=:database and table_schema='public'", array(
            ':database' => $this->database->getDatabaseName()
        ));
        foreach ($tables as $table) {
            $this->database->executeQuery("truncate {$table['table_name']} cascade");
        }
    }

    /**
     * Initializes the database connection
     * @return boolean
     */
    private function initDatabaseConnection() {
        $configFile = realpath($this->getConfigFolderLocation() . '/' . $this->getApplicationName() . "-{$this->env}-config.php");
        if (file_exists($configFile)) {
            $config = include($configFile);
            $this->database = new Database($config['database']);
            try {
                $this->database->executeQuery("select version()");
                $this->output->writeln("<info>Connected to Database</info>");
                return true;
            } catch (DatabaseQueryException $e) {
                $this->output->writeln("<error>{$e->getMessage()}</error>");
            }
        } else {
            $this->output->writeln("<error>Unable to find the config file {$configFile}</error>");
            return false;
        }
    }

    /**
     * Gets the current version of the database and creates the sys_db_version if needed
     * @return strinf
     */
    protected function getCurrentDatabaseVersion() {
        $sys_db_version = $this->database->executeQuery("select * from information_schema.tables where table_name='sys_db_version' and table_catalog=:database", array(
            ':database' => $this->database->getDatabaseName()
        ));
        if (count($sys_db_version) === 0) {
            $sys_db_version_table = <<<EOT
                    create table sys_db_version (
                            id serial not null primary key,
                            version varchar not null unique,
                            filename varchar not null unique,
                            date_patched timestamp not null default now()
                    );
EOT;
            $this->database->executeQuery($sys_db_version_table);
            return -1;
        } else {
            $version = $this->database->executeQuery("select * from sys_db_version order by id desc limit 1");
            if (count($version) === 0) {
                return -1;
            } else {
                return $version[0]['version'];
            }
        }
    }

}
