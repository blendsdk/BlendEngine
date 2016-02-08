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
                ->addOption('configclass', 'c'
                        , InputArgument::OPTIONAL
                        , 'A config class that is going to be used to generated the models');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->loadConfig($input, $output);
    }

    /**
     * Load the configuration file if possible
     * @param InputInterface $input
     * @throws \InvalidArgumentException
     */
    private function loadConfig(InputInterface $input, OutputInterface $output) {
        $configClass = $input->getOption('configclass');
        if (is_null($configClass)) {
            $configClass = ModelBuilderDefaultConfig::class;
        };
        $container = new Container();
        $this->config = $container->get($configClass,[
            'projectFolder' => $this->getApplication()->getProjectFolder()
        ]);
        $output->writeln('<info>Useing the ' . get_class($this->config) . ' as configuration</info>');
    }

    private function validateInputConfig($config) {
        return $config;
    }

}
