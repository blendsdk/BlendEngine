<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Command;

use Blend\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Blend\DataModelBuilder\Command\ModelBuilderDefaultConfig;
use Blend\Component\Database\Database;
use Blend\DataModelBuilder\Schema\SchemaReader;
use Blend\DataModelBuilder\Schema\Schema;
use Blend\DataModelBuilder\Schema\Relation;
use Blend\DataModelBuilder\Template\ModelTemplate;
use Blend\DataModelBuilder\Template\Template;

/**
 * Data Model Layer generator. This class will load the schemas, tables, etc...
 * from the database and generate a DAL.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DataModelCommand extends Command {

    private $schemas;

    /**
     * @var ModelBuilderConfig
     */
    private $config = null;
    private $templateFolder;
    private $dbToPHPTypes = [
    ];

    protected function configure() {
        $this->setName('datamodel:generate')
                ->setDescription('Generates a Data Model Layer from the current database')
                ->addOption('configclass', 'c'
                        , InputArgument::OPTIONAL
                        , 'A config class that is going to be used to generated the models');
    }

    protected function initialize(InputInterface $input, OutputInterface $output) {
        parent::initialize($input, $output);
        $this->templateFolder = realpath(dirname(__FILE__) . '/../Templates');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->loadConfig();
        if ($this->loadDatabaseSchema()) {
            foreach ($this->schemas as $schemaName => $schema) {
                /* @var $schema \Blend\DataModelBuilder\Schema\Schema */
                $this->output->writeln("<info>Generating Models for schema [{$schemaName}]</info>");
                $this->generateClasses($schema);
            }
        }
    }

    protected function generateClasses(Schema $schema) {
        foreach ($schema->getRelations() as $relation) {
            $this->generateModel($relation, $this->isRelationCustomized($relation), !$schema->getIsSingleSchema());
        }
    }

    protected function generateModel(Relation $relation, $customized = false, $includeSchema = false) {

        if ($customized) {
            $classes = array(
                'Model' => array(
                    'namespace' => $this->createFQNamespace($relation, 'Model', $includeSchema, false),
                    'uses' => ['Blend\Component\Model\Base\\' . $relation->getName(true) . ' as ' . $relation->getName(true) . 'Base'],
                    'baseClass' => $relation->getName(true) . 'Base'
                ),
                'Model\Base' => array(
                    'namespace' => $this->createFQNamespace($relation, 'Model\Base', $includeSchema, false),
                    'uses' => ['Blend\Component\Model\Model'],
                    'baseClass' => 'Model',
                    'classmod' => 'abstract',
                    'genprops' => true
                )
            );
        } else {
            $classes = array(
                'Model' => array(
                    'namespace' => $this->createFQNamespace($relation, 'Model', $includeSchema, false),
                    'uses' => ['Blend\Component\Model\Model'],
                    'baseClass' => 'Model',
                    'genprops' => true
                )
            );
        }

        foreach ($classes as $type => $class) {
            $template = new ModelTemplate();
            $template
                    ->setNamespace($class['namespace'])
                    ->addUsedClass($class['uses'])
                    ->setFQRN($relation->getFQRN())
                    ->setClassname($relation->getName(true))
                    ->setBaseClass($class['baseClass'])
                    ->setClassModifier(isset($class['classmod']) ? $class['classmod'] : null);
            if (isset($class['genprops'])) {
                foreach ($relation->getColumns() as $column) {
                    $template->addProperty($column->getName());
                }
            }
            $outFile = $this->prepareOutput($template, $relation, $type, $includeSchema);
            $template->render($outFile);
            $this->output->writeln("Generate {$class['classname']} ({$type})");
        }
    }

    private function translateTypeToPHP($type) {
        return isset($this->dbToPHPTypes[$type]) ?
                $this->dbToPHPTypes[$type] : null;
    }

    private function prepareOutput(Template $template, Relation $relation, $type, $includeSchema = false) {
        $folder = $this->config->getTargetRootFolder()
                . '/' . $this->config->getModelRootNamespace()
                . ($includeSchema ? '/' . $relation->getSchemaName(true) : '')
                . '/' . $type;
        $this->fileSystem->ensureFolder($folder);
        return $folder . '/' . $relation->getName(true) . '.php';
    }

    private function createFQNamespace(Relation $relation, $type, $includeSchema = false) {
        return $this->config->getApplicationNamespace()
                . '\\'
                . $this->config->getModelRootNamespace()
                . ($includeSchema ? '\\' . $relation->getSchemaName(true) : '')
                . '\\' . $type;
    }

    private function isRelationCustomized(Relation $relation) {

        $customizedModels = $this->config->getCustomizedRelationList();
        if (!is_array($customizedModels)) {
            $customizedModels = [];
        }

        return (in_array($relation->getName(), $customizedModels) || in_array($relation->getFQRN(), $customizedModels));
    }

    /**
     * Load the Schema information from the database that is configured in the
     * config.php
     * @return boolean
     */
    protected function loadDatabaseSchema() {
        $database = new Database([
            'username' => $this->getConfig('database.username'),
            'password' => $this->getConfig('database.password'),
            'database' => $this->getConfig('database.database'),
            'host' => $this->getConfig('database.host'),
            'port' => $this->getConfig('database.port'),
        ]);
        $schemaReader = new SchemaReader($database);
        $schemas = $schemaReader->load();
        if (is_null($this->config->getSchemaListToGenerate())) {
            $this->schemas = $schemas;
        } else {
            foreach ($this->config->getSchemaListToGenerate() as $name) {
                if (isset($schemas[$name])) {
                    $this->schemas[$name] = $schemas[$name];
                } else {
                    $this->output->writeln("<error>There is no [{$name}] schema in this database.</error>");
                }
            }
        }
        if (!isset($this->schemas['public'])) {
            $this->output->writeln("<warn>WARNING: The [public] schema from your database was not selected!</warn>");
        }
        return true;
    }

    /**
     * Load the configuration file if possible
     * @param InputInterface $input
     * @throws \InvalidArgumentException
     */
    private function loadConfig() {
        $configClass = $this->input->getOption('configclass');
        if (is_null($configClass)) {
            $configClass = ModelBuilderDefaultConfig::class;
        };
        try {
            $this->config = $this->container->get($configClass, [
                'projectFolder' => $this->getApplication()->getProjectFolder()
            ]);
        } catch (ReflectionException $ex) {
            $this->output->writeln([
                "<warn>Unable to load the provided configuration [{$configClass}]",
                "Will continue with the default configuration."
            ]);
            $configClass = ModelBuilderDefaultConfig::class;
            $this->config = $this->container->get($configClass, [
                'projectFolder' => $this->getApplication()->getProjectFolder()
            ]);
        }
        $this->output->writeln('<info>Using the ' . get_class($this->config) . ' as configuration</info>');
    }

}
