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

use Blend\Component\Configuration\Configuration;
use Blend\Component\DI\Container;
use Blend\Component\Filesystem\Filesystem;
use Blend\Framework\Factory\CommonLoggerFactory;
use Blend\Framework\Factory\ConfigurationFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command as CommandBase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CommandBase in the base class for all application level command in
 * BlendEngine.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Command extends CommandBase
{
    /**
     * DI Container for handling services and object.
     *
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

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->container = new Container();
        $this->fileSystem = new Filesystem();
        $this->initContainer();
    }

    /**
     * Initializes the DI container.
     */
    protected function initContainer()
    {
        $this->container->setScalars(array(
            'rootFolder' => $this->getApplication()->getProjectFolder(),
            'logFolder' => $this->getApplication()->getProjectFolder().'/var/log',
            'logName' => 'console',
            'logLevel' => LogLevel::DEBUG,
        ));

        // By setting to the debug mode we ignore the cached config
        $this->container->defineSingletonWithInterface(Configuration::class, ConfigurationFactory::class, array('debug' => true));

        $this->container->defineSingletonWithInterface(
                LoggerInterface::class, CommonLoggerFactory::class);
    }

    /**
     * Returns a configuration variable.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getConfig($name, $default = null)
    {
        /* @var $config Configuration */
        $config = $this->container->get(Configuration::class);

        return $config->get($name, $default);
    }
}
