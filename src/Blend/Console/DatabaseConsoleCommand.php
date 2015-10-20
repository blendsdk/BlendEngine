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
use Blend\Database\PostgreSQL\Table;
use Blend\Database\PostgreSQL\Column;

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

    /**
     * @param type $table_schema
     * @return Tables[]
     */
    protected function loadTables($table_schema = 'public') {
        $tablesQuery = "select * from information_schema.tables where table_schema = :table_schema and table_catalog = :table_catalog";
        $tableQueryParams = array(
            ':table_schema' => $table_schema,
            ':table_catalog' => $this->database->getDatabaseName()
        );
        $tables = array();
        $list = $this->database->executeQuery($tablesQuery, $tableQueryParams);
        foreach ($list as $index => $record) {
            $table = new Table($record);
            $this->loadColumns($table);
            $this->loadConstraints($table);
            $tables[$table->getTableName()] = $table;
        }
        return $tables;
    }


    private function loadConstraints(Table $table) {
        $tableConstQuery = "select * from information_schema.table_constraints where constraint_type in ('UNIQUE','PRIMARY KEY','FOREIGN KEY') and table_schema = :table_schema and table_catalog = :table_catalog and table_name = :table_name";
        $tableConstQueryParams = array(
            ':table_schema' => $table->getTableSchema(),
            ':table_catalog' => $table->getTableCatalog(),
            ':table_name' => $table->getTableName()
        );
        $tableConsts = $this->database->executeQuery($tableConstQuery, $tableConstQueryParams);
        foreach ($tableConsts as $tableConst) {
            $a_name =  $tableConst['constraint_name'];
            $a_tname = $table->getTableName();
            if (stripos($tableConst['constraint_name'], $table->getTableName()) === 0) {
                $constColumnQuery = "select * from information_schema.constraint_column_usage where table_schema = :table_schema and table_catalog = :table_catalog and table_name = :table_name and constraint_name = :constraint_name";
                $constColumnParams = array(
                    ':table_schema' => $tableConst['table_schema'],
                    ':table_catalog' => $tableConst['table_catalog'],
                    ':table_name' => $tableConst['table_name'],
                    ':constraint_name' => $tableConst['constraint_name'],
                );
                $constColumns = $this->database->executeQuery($constColumnQuery, $constColumnParams);
                foreach ($constColumns as $constColumn) {
                    $table->addKeyColumn($constColumn);
                }
            }
        }
    }

    private function loadColumns(Table $table) {
        $columnsQuery = "select * from information_schema.columns where table_schema = :table_schema and table_catalog = :table_catalog and table_name = :table_name";
        $columnsQueryParams = array(
            ':table_schema' => $table->getTableSchema(),
            ':table_catalog' => $table->getTableCatalog(),
            ':table_name' => $table->getTableName()
        );
        $columns = $this->database->executeQuery($columnsQuery, $columnsQueryParams);
        foreach ($columns as $record) {
            $column = new Column($record);
            $table->addColumn($column);
        }
        return $table;
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
