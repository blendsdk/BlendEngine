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
                ->setDescription('Generate Schema and Model classes')
                ->addArgument('table', InputArgument::OPTIONAL, 'LIKE select criteria to select the table names: %( = ALL)', '%');
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
            'columns' => array_merge(array($table_const), $columns)
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
