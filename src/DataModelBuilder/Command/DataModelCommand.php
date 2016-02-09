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
                $targetPath = $this->createSchemaTargetPath($schema);
                $this->generateModels($schema, $targetPath);
            }
        }
    }

    protected function generateModels(Schema $schema, $targetPath) {

        foreach ($schema->getRelations() as $relation) {
            $this->generateModel($relation, $this->isRelationCustomized($relation), !$schema->getIsSingleSchema());
        }
    }

    protected function generateModel(Relation $relation, $customized = false, $includeSchema = false) {

        if ($customized) {
            
        } else {
            list($namespace, $folder, $file) = $this->createNamespace($relation, $includeSchema);
            $this->fileSystem->ensureFolder($folder);
            $mt = new ModelTemplate();
            $mt->setNamespace($namespace)
                    ->setClassname($relation->getName(true))
                    ->setBaseClass('Model');
            $mt->render($file);
        }
    }

    private function createNamespace(Relation $relation, $includeSchema = false) {
        $ns = $this->config->getModelRootNamespace()
                . (($includeSchema ? '\\' . $relation->getSchemaName(true) : ''));
        $folder = $this->config->getTargetRootFolder() . '/' . $ns;
        $file = $folder . '/' . $relation->getName(true) . '.php';
        return [$this->config->getApplicationNamespace() . '\\' . $ns, $folder, $file];
    }

    private function isRelationCustomized(Relation $relation) {

        $customizedModels = $this->config->getCustomizedRelationList();
        if (!is_array($customizedModels)) {
            $customizedModels = [];
        }

        return (in_array($relation->getName(), $customizedModels) || in_array($relation->getFQRN(), $customizedModels));
    }

    /**
     * Creates a destination forlder for a given schema
     * @param Schema $schema
     * @return string
     */
    protected function createSchemaTargetPath(Schema $schema, $appendName = true) {
        $root = $this->config->getTargetRootFolder()
                . (!$schema->getIsSingleSchema() ? '/' . $schema->getName(true) : '');
        $this->fileSystem->ensureFolder($root);
        return $root;
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
        $this->config = $this->container->get($configClass, [
            'projectFolder' => $this->getApplication()->getProjectFolder()
        ]);
        $this->output->writeln('<info>Using the ' . get_class($this->config) . ' as configuration</info>');
    }

}
