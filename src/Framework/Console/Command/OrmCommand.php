<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Console\Command;

use Blend\Component\Database\Database;
use Blend\Component\Database\Schema\Schema;
use Blend\Component\Database\Schema\Relation;
use Blend\Component\Database\Schema\SchemaReader;
use Blend\Framework\Factory\DatabaseFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Blend\Framework\Console\Command\Orm\OrmConfig;
use Blend\Framework\Console\Command\Orm\ModelClassTemplate;
use Blend\Framework\Console\Command\Orm\ClassTemplate;

/**
 * OrmCommand creates a data access layer based on a PostgreSQL
 * database schema.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class OrmCommand extends Command
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @var string
     */
    private $rootFolder;

    /**
     * @var OrmConfig
     */
    private $ormConfig;

    /**
     * @var Schema[]
     */
    private $schemas;

    protected function configure()
    {
        $this->setName('ormgenerate')
                ->setDescription('Generates data objects and data factories from your database ')
                ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The configuration class file');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        /* @var $database Database */
        $this->database = $this->container->get(DatabaseFactory::class);
        $this->container->setScalar(Database::class, $this->database);
        $this->rootFolder = $this->container->get('rootFolder');
        $this->schemas = $this->container->get(SchemaReader::class)->read();
    }

    /**
     * Normalizes named to de-conflict with built-in names
     * @param type $name
     * @return string
     */
    private function normalizeName($name)
    {
        if ($name === 'public') {
            return 'common';
        } else {
            return $name;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadOrmConfig();
        $this->validateConfig();

        $this->ormConfig->setRootFolder($this->rootFolder);
        $this->fileSystem->ensureFolder($this->ormConfig->getOutputFolder());

        foreach ($this->ormConfig->getTables() as $tableItem) {
            list($schemaName, $tableName) = $tableItem;
            $schemaFolder = $this->getSchemaFolderName($this->normalizeName($schemaName));
            $modelFolder = $schemaFolder . DIRECTORY_SEPARATOR . 'Model';
            $factoryFolder = $schemaFolder . DIRECTORY_SEPARATOR . 'Factory';
            $this->fileSystem->ensureFolder(array($schemaFolder, $modelFolder, $factoryFolder));

            $schema = $this->schemas[$schemaName];
            $relations = $schema->getRelations();
            $relation = $relations[$tableName];

            $this->output->writeln("<info>Generating {$relation->getName()}</info>");
            $this->createModel($relation, $modelFolder);
            $this->createFactory($relation, $factoryFolder);
        }
    }

    private function getSchemaNamespace(Relation $relation)
    {
        return "Database\\" . str_identifier($this->normalizeName($relation->getSchema()->getName()));
    }

    private function createFactory(Relation $relation, $factoryFolder)
    {

    }

    private function setupTemplate(Relation $relation, ClassTemplate $template)
    {
        $template->setApplicationNamespace($this->getApplicationNamespace());
        $template->setClassNamespace($this->getSchemaNamespace($relation));
        $template->setClassName(str_identifier($relation->getName()));
        $template->setFQRN($relation->getFQRN());
    }

    private function createModel(Relation $relation, $modelFolder)
    {
        $template = new ModelClassTemplate();
        $this->setupTemplate($relation, $template);
        $template->setBaseClassName('Model');
        $template->addUses(array(
            'Blend\Component\Model\Model'
        ));
        foreach ($relation->getColumns() as $column) {
            $template->addProperty($column->getName(), $column->getType());
        }
        $template->renderToFile($modelFolder . '/' . str_identifier($relation->getName()) . 'Model.php');
    }

    /**
     * Gets the main namespace in which this application is running from
     * @return type
     */
    private function getApplicationNamespace()
    {
        $ns = explode('\\', get_class($this->getApplication()));
        return $ns[0];
    }

    private function getSchemaFolderName($schemaName)
    {
        return $this->ormConfig->getOutputFolder() . DIRECTORY_SEPARATOR . str_identifier($schemaName);
    }

    private function validateConfig()
    {
        $this->output->writeln('<info>Validating the configuration.</info>');

        $this->validateRelations();
        $this->assertRelations();
    }

    /**
     * Gets the ORM configuration file either from the application's root
     * folder .ormconfig.dist or a default configuration when the files does
     * not exist.
     *
     * @throws \LogicException
     */
    private function loadOrmConfig()
    {
        $configFile = $this->rootFolder . '/.ormconfig.dist';
        if ($this->fileSystem->exists($configFile)) {
            $config = include $configFile;
            if (!($config instanceof OrmConfig)) {
                throw new \LogicException("Invalid data access builder configuration file!\nThe configuration did not return a OrmConfig instance!");
            }
            $this->output->writeln("<info>Loading ORM configuration from {$configFile}</info>");
        } else {
            $config = new OrmConfig();
            $config->setOutputFolder('src');
            $this->output->writeln('<info>Creating a default configuration.</info>');
        }
        $this->ormConfig = $config;
    }

    /**
     * Assert if the request objects from the database actually exist
     * @throws \LogicException
     */
    private function assertRelations()
    {
        foreach ($this->ormConfig->getTables() as $item) {
            list($schemaName, $tableName) = $item;
            if (!array_key_exists($schemaName, $this->schemas)) {
                throw new \LogicException("The database does not contain the {$schemaName} schema!");
            }
            $relations = $this->schemas[$schemaName]->getRelations();
            if (!array_key_exists($tableName, $relations)) {
                throw new \LogicException("The {$schemaName} schema does not contain the {$tableName} table!");
            }
        }
    }

    /**
     * Loads relations from the current database if nothing was selected before
     */
    private function validateRelations()
    {
        if (count($this->ormConfig->getTables()) === 0) {
            foreach ($this->schemas as $schema) {
                $relations = $schema->getRelations();
                foreach ($relations as $item) {
                    $this->ormConfig->addTable($item->getName(), $item->getSchema()->getName());
                }
            }
        }
    }
}

function print_php_header()
{
    echo "<?php\n\n";
}
