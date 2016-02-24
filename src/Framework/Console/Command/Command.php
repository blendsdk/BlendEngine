<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Console\Command;

use Symfony\Component\Console\Command\Command as CommandBase;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Blend\Component\DI\Container;
use Blend\Component\Configuration\Configuration;
use Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Blend\Component\Filesystem\Filesystem;

/**
 * CommandBase in the base class for all application level command in 
 * BlendEngine.
 * 
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Command extends CommandBase {

    /**
     * DI Container for handling services and object
     * @var Container; 
     */
    protected $container;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;
        $this->container = new Container();
        $this->fileSystem = new Filesystem();
        $this->initContainer();
    }

    /**
     * Initializes the DI container
     */
    protected function initContainer() {
        $this->container->singleton(Configuration::class, [
            'class' => Configuration::class,
            'factory' => function() {
                $filename = realpath($this->getApplication()->getProjectFolder()
                        . '/config/config.json');
                return Configuration::createFromFile($filename);
            }
        ]);

        $this->container->singleton(LoggerInterface::class, [
            'factory' => function() {
                $fs = new Filesystem();
                $logfolder = $this->getApplication()->getProjectFolder() . '/var/log';
                $logname = $this->getApplication()->getName() . '-console';
                $fs->ensureFolder($logfolder);
                $log = new Logger($logname);
                $log->pushHandler(new StreamHandler($logfolder . '/' . $logname . '.log', Logger::DEBUG));
                return $log;
            }
        ]);
    }

    /**
     * Returns a configuration variable
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getConfig($name, $default = null) {
        /* @var $config Configuration */
        $config = $this->container->get(Configuration::class);
        return $config->get($name, $default);
    }

}
