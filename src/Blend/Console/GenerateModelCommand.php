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

    protected abstract function getCommandFolder();

    protected abstract function getCommandNamespace();

    protected function configure() {
        parent::configure();
        $this->setName('database:model')
                ->setDescription('Generate Schema and Model classes');
    }

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

    protected function createInitDbCommand($tables) {
        $services = array();
        $models = array();
        $properties = array();

        $baseFilename = "{$this->getCommandFolder()}/DatabaseInitDbCommandBase.php";
        $cmdFilename = "{$this->getCommandFolder()}/DatabaseInitDbCommand.php";

        foreach ($tables as $table) {
            $models[] = "{$table->getModelNamespace()}\\{$table->getModelClassName()}";
            $services[] = "{$table->getServiceNamespace()}\\{$table->getServiceClassName()}";
            $properties[] = array(
                'name' => $this->createPropertyName($table->getTableName(), 'Service'),
                'type' => $table->getServiceClassName()
            );
        }

        sort($models);
        sort($services);

        $baseClass = $this->renderFile(dirname(__FILE__) . '/templates/initdb_base.php', array(
            'namespace' => $this->getCommandNamespace(),
            'usages' => $services,
            'properties' => $properties,
            'application_name' => $this->getApplicationName(),
            'config_folder' => $this->getOutputFolder()
        ));
        file_put_contents($baseFilename, $baseClass);

        if (!file_exists($cmdFilename)) {
            $cmdClass = $this->renderFile(dirname(__FILE__) . '/templates/initdb.php', array(
                'namespace' => $this->getCommandNamespace(),
                'usages' => $models,
            ));

            file_put_contents($cmdFilename, $cmdClass);
        }
    }

    protected function executeDatabaseOperation(InputInterface $input, OutputInterface $output) {

        $usages = array();
        $proprties = array();

        $tables = $this->loadTables();
        $this->output->writeln("<info>" . count($tables) . " table(s) found!<info>");
        foreach ($tables as $table) {
            $this->createSchemaFile($table);
            $this->createModelFile($table);
            $this->createServiceFile($table);
        }

        $this->createInitDbCommand($tables);
    }

    private function createPropertyName($name, $postfix) {
        $name = $this->ucWords(str_replace('sys_', '', $name), '', $postfix);
        $name[0] = strtolower($name[0]);
        return $name;
    }

}
