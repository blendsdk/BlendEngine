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
use Blend\Component\Database\Schema\Constraint;
use Blend\Component\Database\Schema\Relation;
use Blend\Component\Database\Schema\Schema;
use Blend\Component\Database\Schema\SchemaReader;
use Blend\Framework\Console\Command\Orm\ClassTemplate;
use Blend\Framework\Console\Command\Orm\FactoryClassTemplate;
use Blend\Framework\Console\Command\Orm\Method;
use Blend\Framework\Console\Command\Orm\ModelClassTemplate;
use Blend\Framework\Console\Command\Orm\OrmConfig;
use Blend\Framework\Factory\DatabaseFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
     * Normalizes named to de-conflict with built-in names.
     *
     * @param type $name
     *
     * @return string
     */
    private function normalizeName($name)
    {
        if ($name === 'public') {
            return 'common';
        } else {
            return str_replace('_id', '_ID', $name);
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
        return 'Database\\' . str_identifier($this->normalizeName($relation->getSchema()->getName()));
    }

    private function setupTemplate(Relation $relation, ClassTemplate $template)
    {
        $template->setApplicationNamespace($this->getApplicationNamespace());
        $template->setClassNamespace($this->getSchemaNamespace($relation));
        $template->setClassName(str_identifier($relation->getName()));
        $template->setFQRN($relation->getFQRN());
    }

    private function createFactory(Relation $relation, $factoryFolder)
    {
        $template = new FactoryClassTemplate();
        $this->setupTemplate($relation, $template);
        $template->setBaseClassName('Factory');
        $template->addUses(array(
            'Blend\Component\Database\Database',
            'Blend\Component\Database\Factory\Factory',
        ));
        $template->setModelClass(str_identifier($relation->getName()));

        foreach ($relation->getConstraintsByType() as $type => $constraints) {
            foreach ($constraints as $constraint) {
                $this->createMethod($relation, $constraint, $template);
            }
        }

        $template->renderToFile($factoryFolder . '/' . str_identifier($relation->getName()) . 'Factory.php');
    }

    /**
     * Creates a Method from a Constraint.
     *
     * @param Constraint $constraint
     *
     * @return Method
     */
    private function createMethod(Relation $relation, Constraint $constraint, FactoryClassTemplate $template)
    {
        if ($constraint->getType() === 'FOREIGN KEY') {
            $get_method = $this->newMethod($constraint, 'getManyBy', 'Gets many records from ' . $relation->getFQRN() . ' table');
            $callParams = $get_method->getCallArgumentArray();
            $get_method->setContent("return \$this->getManyBy(Factory::ALL_COLUMNS,$callParams,\$orderDirective,\$offsetLimitDirective);");
            $get_method->addParameter('orderDirective', array('string', 'null'));
            $get_method->addParameter('offsetLimitDirective', array('mixed', 'null'));
            $template->addMethod($get_method);

            $del_method = $this->newMethod($constraint, 'deleteManyBy', 'Deletes many records from ' . $relation->getFQRN() . ' table');
            $callParams = $del_method->getCallArgumentArray();
            $del_method->addParameter('stmtResult', array('string', 'null'));
            $del_method->setContent("return \$this->deleteManyBy($callParams,\$stmtResult);");
            $template->addMethod($del_method);

            $count_method = $this->newMethod($constraint, 'countBy', 'Counts records from ' . $relation->getFQRN() . ' table');
            $callParams = $count_method->getCallArgumentArray();
            $count_method->setContent("return \$this->countBy($callParams);");
            $template->addMethod($count_method);
        } else {
            $get_method = $this->newMethod($constraint, 'getOneBy', 'Gets a single record from ' . $relation->getFQRN() . ' table');
            $callParams = $get_method->getCallArgumentArray();
            $get_method->setContent("return \$this->getOneBy(Factory::ALL_COLUMNS,$callParams);");
            $template->addMethod($get_method);

            $del_method = $this->newMethod($constraint, 'deleteOneBy', 'Deletes a single record from ' . $relation->getFQRN() . ' table');
            $callParams = $del_method->getCallArgumentArray();
            $del_method->setContent("return \$this->deleteManyBy($callParams);");
            $template->addMethod($del_method);
        }
    }

    private function newMethod(Constraint $constraint, $prefix, $description)
    {
        $method = new Method();
        $method->setDescription($description);
        $name = array();
        foreach ($constraint->getColumns() as $column) {
            $name[] = str_identifier($this->normalizeName($column->getName()));
            $method->addParameter(strtolower($column->getName()), strtolower($column->getType()));
        }
        $method->setName($prefix . implode('And', $name));

        return $method;
    }

    private function createModel(Relation $relation, $modelFolder)
    {
        $template = new ModelClassTemplate();
        $this->setupTemplate($relation, $template);
        $template->setBaseClassName('Model');
        $template->addUses(array(
            'Blend\Component\Model\Model',
        ));
        foreach ($relation->getColumns() as $column) {
            $template->addProperty($this->normalizeName($column->getName()), $column->getType());
        }
        $template->renderToFile($modelFolder . '/' . str_identifier($relation->getName()) . 'Model.php');
    }

    /**
     * Gets the main namespace in which this application is running from.
     *
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
     * Assert if the request objects from the database actually exist.
     *
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
     * Loads relations from the current database if nothing was selected before.
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
