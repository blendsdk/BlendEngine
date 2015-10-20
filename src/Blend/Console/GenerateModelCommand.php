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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Blend\Database\PostgreSQL\Table;
use Blend\Database\PostgreSQL\Column;

/**
 * Description of GenerateModelCommand
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class GenerateModelCommand extends DatabaseConsoleCommand {

    protected abstract function getNamespace();

    protected abstract function getOutputFolder();

    protected function configure() {
        parent::configure();
        $this->setName('database:model')
                ->setDescription('Generate Schema and Model classes');
    }

//    protected function createSchemaFile($table_name, $columns, $primary_key = array()) {
//
//        $table_const = array(
//            'column_name' => $table_name,
//            'column_name_upr' => 'TABLE_NAME',
//            'data_type' => 'string',
//            'description' => "Schema for the {$table_name} table"
//        );
//
//        foreach ($primary_key as $idx => $key) {
//            $primary_key[$idx] = "'{$key}'";
//        }
//
//        $class_name = strtoupper($table_name) . '_SCHEMA';
//        $class = $this->renderFile(dirname(__FILE__) . '/templates/table_schema.php', array(
//            'namespace' => $this->getNamespace() . '\Schema',
//            'class_name' => $class_name,
//            'table_name' => $table_name,
//            'columns' => array_merge(array($table_const), $columns),
//            'primary_key' => $primary_key
//        ));
//
//        $folder = "{$this->getOutputFolder()}/Schema";
//        @mkdir($folder, 0777, true);
//        $class_file = "{$folder}/{$class_name}.php";
//        file_put_contents($class_file, $class);
//    }
//
//    protected function createModelFile($class_name, $base_namespace) {
//
//        $namespace = $this->getNamespace() . '\Model';
//        $class = $this->renderFile(dirname(__FILE__) . '/templates/model.php', array(
//            'namespace' => $namespace,
//            'class_name' => $class_name,
//            'base_namespace' => $base_namespace
//        ));
//
//        $folder = "{$this->getOutputFolder()}/Model";
//        @mkdir($folder, 0777, true);
//        $class_file = "{$folder}/{$class_name}.php";
//        if (!file_exists($class_file)) {
//            file_put_contents($class_file, $class);
//        }
//    }
//
//    protected function createFunctionSignature($table_name, $prefix, $params = array()) {
//        $args = array();
//        //$sql_args = array();
//
//        foreach ($params as $name) {
//            $sc_name = 'SC::' . strtoupper($name);
//            $args[] = "\${$name}";
//            $sql_args[] = "                $sc_name . '= :{$name}'";
//            $qry_args[] = "            ':{$name}' => \${$name}";
//        }
//        return array(
//            'name' => $this->ucWords(implode(' And ', $params), $prefix),
//            'args' => $args,
//            'sql_args' => $sql_args,
//            'qry_args' => $qry_args
//        );
//    }
//
//    protected function createModelServiceBase($table, $record_class) {
//
//        $table_name = $table['table_name'];
//        $class_name = $this->ucWords($table_name);
//        $namespace = $this->getNamespace() . '\Service\Base';
//        $class = $this->renderFile(dirname(__FILE__) . '/templates/service_base.php', array(
//            'namespace' => $namespace,
//            'class_name' => "{$class_name}Service",
//            //'table_name' => $table_name,
//            //'columns' => $columns,
//            'schema_class_name' => strtoupper($table_name) . '_SCHEMA',
//            'schema_namespace' => $this->getNamespace() . '\Schema',
//            'record_class' => $record_class,
//            'primary_getter_function' => $this->createFunctionSignature($table_name, 'getBy', $table['primary'])
//        ));
//
//        $folder = "{$this->getOutputFolder()}/Service/Base";
//        @mkdir($folder, 0777, true);
//        $class_file = "{$folder}/{$class_name}Service.php";
//        file_put_contents($class_file, $class);
//    }
//
//    protected function createModelBaseFile($table, $columns) {
//
//        $table_name = $table['table_name'];
//        $class_name = $this->ucWords($table_name);
//        $namespace = $this->getNamespace() . '\Model\Base';
//        $class = $this->renderFile(dirname(__FILE__) . '/templates/model_base.php', array(
//            'namespace' => $namespace,
//            'class_name' => "{$class_name}",
//            'table_name' => $table_name,
//            'columns' => $columns,
//            'schema_class_name' => strtoupper($table_name) . '_SCHEMA',
//            'schema_namespace' => $this->getNamespace() . '\Schema'
//        ));
//
//        $folder = "{$this->getOutputFolder()}/Model/Base";
//        @mkdir($folder, 0777, true);
//        $class_file = "{$folder}/{$class_name}.php";
//        file_put_contents($class_file, $class);
//
//        $this->createModelFile($class_name, $namespace);
//        $record_class = array(
//            'use' => $this->getNamespace() . "\Model\\{$class_name}",
//            'class_name' => $class_name
//        );
//        $this->createModelServiceBase($table, $record_class);
//    }

    protected function createSchemaFile(Table $table) {
        $this->output->writeln("<info>Creating Schema Class for {$table->getTableName()}<info>");

        $table->setSchemaNamespace($this->getNamespace() . '\Schema');
        $class = $this->renderFile(dirname(__FILE__) . '/templates/table_schema.php', array(
            'table' => $table
        ));

        $folder = "{$this->getOutputFolder()}/Schema";
        @mkdir($folder, 0777, true);
        $class_file = "{$folder}/{$table->getSchemaClassName()}.php";
        file_put_contents($class_file, $class);
    }

    protected function createModelFile(Table $table) {

        $baseNamespace = $this->getNamespace() . '\Model\Base';
        $table->setModelBaseNamespace($baseNamespace);
        $baseClass = $this->renderFile(dirname(__FILE__) . '/templates/model_base.php', array(
            'table' => $table
        ));
        $baseFolder = "{$this->getOutputFolder()}/Model/Base";
        @mkdir($baseFolder, 0777, true);
        $baseClassFile = "{$baseFolder}/{$table->getClassName()}.php";
        file_put_contents($baseClassFile, $baseClass);

        $modelFolder = "{$this->getOutputFolder()}/Model";
        $modelClassFile = "{$modelFolder}/{$table->getClassName()}.php";
        $modelNamespace = $this->getNamespace() . '\Model';
        $table->setModelNamespace($modelNamespace);
        if (!file_exists($modelClassFile)) {
            $modelClass = $this->renderFile(dirname(__FILE__) . '/templates/model.php', array(
                'table' => $table
            ));

            @mkdir($modelFolder, 0777, true);
            file_put_contents($modelClassFile, $modelClass);
        }
    }

    protected function createServiceFile(Table $table) {

        $baseNamespace = $this->getNamespace() . '\Service\Base';
        $table->setServiceBaseNamespace($baseNamespace);
        $baseClass = $this->renderFile(dirname(__FILE__) . '/templates/service_base.php', array(
            'table' => $table
        ));
        $baseFolder = "{$this->getOutputFolder()}/Service/Base";
        @mkdir($baseFolder, 0777, true);
        $baseClassFile = "{$baseFolder}/{$table->getServiceClassName()}.php";
        file_put_contents($baseClassFile, $baseClass);

        $modelFolder = "{$this->getOutputFolder()}/Service";
        $modelClassFile = "{$modelFolder}/{$table->getServiceClassName()}.php";
        $modelNamespace = $this->getNamespace() . '\Service';
        $table->setServiceNamespace($modelNamespace);
        if (!file_exists($modelClassFile)) {
            $modelClass = $this->renderFile(dirname(__FILE__) . '/templates/service.php', array(
                'table' => $table
            ));

            @mkdir($modelFolder, 0777, true);
            file_put_contents($modelClassFile, $modelClass);
        }
    }

    protected function executeDatabaseOperation(InputInterface $input, OutputInterface $output) {

        $tables = $this->loadTables();
        $this->output->writeln("<info>" . count($tables) . " table(s) found!<info>");
        foreach ($tables as $table) {
            $this->createSchemaFile($table);
            $this->createModelFile($table);
            $this->createServiceFile($table);
        }
    }

}
