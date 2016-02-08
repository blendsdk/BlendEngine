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
use Blend\Component\DI\Container;
use Blend\Component\Configuration\Configuration;
use Blend\Component\Database\Database;
use Blend\DataModelBuilder\Schema\SchemaReader;
use Blend\Component\Exception\InvalidConfigException;

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

    protected function configure() {
        $this->setName('datamodel:generate')
                ->setDescription('Generates a Data Model Layer from the current database')
                ->addOption('configclass', 'c'
                        , InputArgument::OPTIONAL
                        , 'A config class that is going to be used to generated the models');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->loadConfig();
        if ($this->loadDatabaseSchema()) {

        }
    }

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
