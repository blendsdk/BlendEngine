<?php

namespace Blend\Framework\Console\Command;

use Blend\Framework\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Blend\Framework\Factory\DatabaseFactory;
use Blend\Component\Database\Database;
use Blend\Component\Database\Schema\SchemaReader;
use Blend\Framework\Console\Command\DataAccessConfig;

/**
 * DataAccessCommand creates a data access layer based on a PostgreSQL
 * database schema
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DataAccessCommand extends Command
{
    /**
     * @var Database
     */
    private $database;
    private $rootFolder;

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
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /* @var $config DataAccessConfig */
        $config = $this->getDataAccessConfig();

        /* @var $schemaReader SchemaReader */
//        $schemaReader = $this->container->get(SchemaReader::class);
//        $schemas = $schemaReader->read();
//        foreach ($schemas as $schema) {
//            foreach ($schema->getRelations() as $relation) {
//                echo($relation->getName() . "\n");
//            }
//        }
    }

    private function getDataAccessConfig()
    {
        $configFile = $this->rootFolder . '/.dalconfig.dist';
        if ($this->fileSystem->exists($configFile)) {
            $config = include($configFile);
            if(!($config instanceof DataAccessConfig)) {
                throw new \LogicException("Invalid data access builder configuration file!\nThe configuration did not return a DataAccessConfig instance!");
            }
            return $config;
        } else {
            return (new DataAccessConfig())
                            ->setOutputFolder('src/Database');
        }
    }
}
