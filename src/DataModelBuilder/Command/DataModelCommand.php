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
use Blend\DataModelBuilder\Builder\ModelBuilder;
use Blend\DataModelBuilder\Builder\FactoryBuilder;
use Blend\Component\DI\Container;

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

    protected function generateClasses(Schema $schema) {
        $conatiner = new Container();
        foreach ($schema->getRelations() as $relation) {

            $allowCustomize = $this->allowCustomize($relation);
            $rootPath = $this->config->getTargetRootFolder();
            $rootNamespace = $this->config->getModelRootNamespace();
            $appNamespace = $this->config->getApplicationNamespace();

            foreach ([ModelBuilder::class, FactoryBuilder::class] as $builderClass) {
                /* @var $builderClass \Blend\DataModelBuilder\Builder\ClassBuilder */
                $builder = $conatiner->get($builderClass, [
                    'relation' => $relation,
                    'includeSchema' => !$schema->isSingle()
                ]);
                $builder->setApplicationNamespace($appNamespace);
                $builder->setRootNamespace($rootNamespace);
                $builder->setRootPath($rootPath);
                $builder->build($allowCustomize);
            }
        }
    }

    /**
     * Check if the Relation needs to be customized later
     * @param Relation $relation
     * @return type
     */
    private function allowCustomize(Relation $relation) {
        $customizedModels = $this->config->getCustomizedRelationList();
        if (!is_array($customizedModels)) {
            $customizedModels = [];
        }
        return (in_array($relation->getName(), $customizedModels) || in_array($relation->getFQRN(), $customizedModels));
    }

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

    /**
     * Load the Schema information from the database that is configured in the
     * config.php
     * @return boolean
     */
    protected function loadDatabaseSchema() {
        $database = $this->createDatabaseInstance();
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
     * Creates and a new Database instance
     * @return Database
     */
    private function createDatabaseInstance() {
        return new Database([
            'username' => $this->getConfig('database.username'),
            'password' => $this->getConfig('database.password'),
            'database' => $this->getConfig('database.database'),
            'host' => $this->getConfig('database.host'),
            'port' => $this->getConfig('database.port'),
        ]);
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
        }
        try {
            $this->config = $this->container->get($configClass, [
                'projectFolder' => $this->getApplication()->getProjectFolder()
            ]);
        } catch (ReflectionException $ex) {
            $this->output->writeln([
                "<warn>Unable to load the provided configuration [{$configClass}]</warn>",
                "<warn>Will continue with the default configuration.</warn>"
            ]);
            $configClass = ModelBuilderDefaultConfig::class;
            $this->config = $this->container->get($configClass, [
                'projectFolder' => $this->getApplication()->getProjectFolder()
            ]);
        }
        $this->output->writeln('<info>Using the ' . get_class($this->config) . ' as configuration</info>');
    }

}
