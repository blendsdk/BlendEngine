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

/**
 * Description of GenerateSchemaHelperCommand
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class GenerateSchemaHelperCommand extends DatabaseConsoleCommand {

    protected abstract function getNamespace();

    protected abstract function getOutputFolder();

    protected function configure() {
        parent::configure();
        $this->setName('database:schemahelper')
                ->setDescription('Generate Schema helper files')
                ->addArgument('table', InputArgument::OPTIONAL, 'LIKE select criteria to select the table names: %( = ALL)', '%');
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

        return $this->database->executeQuery(
                        $sql, array(
                    ':database' => $this->database->getDatabaseName(),
                    ':table' => $search
        ));
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

    protected function createSchemaFile($table_name, $columns) {

        $table_const = array(
            'column_name' => $table_name,
            'column_name_upr' => 'TABLE_NAME',
            'data_type' => 'string',
            'description' => "Schema for the {$table_name} table"
        );

        $class_name = strtoupper($table_name) . '_SCHEMA';
        $class = $this->renderFile(dirname(__FILE__) . '/templates/table_schema.php', array(
            'namespace' => $this->getNamespace() . '\Schema',
            'class_name' => $class_name,
            'table_name' => $table_name,
            'columns' => array_merge(array($table_const),$columns)
        ));

        $folder = "{$this->getOutputFolder()}/Schema";
        @mkdir($folder, 0777, true);
        $class_file = "{$folder}/{$class_name}.php";
        file_put_contents($class_file, $class);
    }

    protected function createModelFile($class_name, $base_namespace) {

        $namespace = $this->getNamespace() . '\Model';
        $class = $this->renderFile(dirname(__FILE__) . '/templates/model.php', array(
            'namespace' => $namespace,
            'class_name' => $class_name,
            'base_namespace' => $base_namespace
        ));

        $folder = "{$this->getOutputFolder()}/Model";
        @mkdir($folder, 0777, true);
        $class_file = "{$folder}/{$class_name}.php";
        if (!file_exists($class_file)) {
            file_put_contents($class_file, $class);
        }
    }

    protected function createModelBaseFile($table_name, $columns) {

        $class_name = $this->ucWords($table_name);
        $namespace = $this->getNamespace() . '\Model\Base';
        $class = $this->renderFile(dirname(__FILE__) . '/templates/model_base.php', array(
            'namespace' => $namespace,
            'class_name' => "{$class_name}",
            'table_name' => $table_name,
            'columns' => $columns,
            'schema_class_name' => strtoupper($table_name) . '_SCHEMA',
            'schema_namespace' => $this->getNamespace() . '\Schema'
        ));

        $folder = "{$this->getOutputFolder()}/Model/Base";
        @mkdir($folder, 0777, true);
        $class_file = "{$folder}/{$class_name}.php";
        file_put_contents($class_file, $class);

        $this->createModelFile($class_name, $namespace);
    }

    protected function ucWords($string, $prefix = '', $postfix = '') {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        return "{$prefix}{$str}{$postfix}";
    }

    protected function executeDatabaseOperation(InputInterface $input, OutputInterface $output) {

        $tables = $this->getTables($input->getArgument('table'));
        $this->output->writeln("<info>" . count($tables) . " table(s) found!<info>");

        foreach ($tables as $table) {

            $table_name = $table['table_name'];
            $columns = $this->getTableColumns($table_name);
            $this->output->writeln("<info> Generating {$table_name}<info>");

            $this->createSchemaFile($table_name, $columns);
            $this->createModelBaseFile($table_name, $columns);
        }
    }

}
