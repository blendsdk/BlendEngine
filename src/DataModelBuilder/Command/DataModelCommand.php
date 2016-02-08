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

/**
 * Data Model Layer generator. This class will load the schemas, tables, etc...
 * from the database and generate a DAL.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DataModelCommand extends Command {

    private $schemas;
    private $config = null;

    protected function configure() {
        $this->setName('datamodel:generate')
                ->setDescription('Generates a Data Model Layer from the current database')
                ->addOption('config', 'c'
                        , InputArgument::OPTIONAL
                        , 'Configuration file to specify how to generate the DAL');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->loadConfig($input);
    }

    /**
     * Load the configuration file if possible
     * @param InputInterface $input
     * @throws \InvalidArgumentException
     */
    private function loadConfig(InputInterface $input) {
        $configFile = $input->getOption('config');
        if (!is_null($configFile) && !file_exists($configFile)) {
            throw new \InvalidArgumentException("Provided configuration file could not be loaded [{$configFile}]");
        } else {
            $this->config = $this->validateInputConfig(include($configFile));
        }
    }

    private function validateInputConfig($config) {
        return $config;
    }

}
