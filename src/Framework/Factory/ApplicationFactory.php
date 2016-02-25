<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Factory;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Blend\Component\DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Blend\Component\Configuration\Configuration;
use Blend\Component\Filesystem\Filesystem;
use Blend\Framework\Factory\ConfigurationFactory;
use Blend\Framework\Factory\CommonLoggerFactory;
use Blend\Component\DI\ObjectFactoryInterface;

/**
 * ApplicationFactory creates an Application instance. By default
 * the a Application will be instantiated with a RotatingFile logger that is
 * instantiated by CommonLoggerFactory. You can set a differect Logger by
 * implementing (or overriding CommonLoggerFactory)
 * a new LoggerFactoryInterface and passing it to setLoggerFactory
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ApplicationFactory implements ObjectFactoryInterface {

    /**
     * @var Filesystem
     */
    private $filesystem;
    private $rootFolder;
    private $debug;
    private $applicationClass;

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LoggerFactoryInterface
     */
    private $loggerFactory;

    public function __construct($applicationClass, $rootFolder, $debug = false, $loggerFactory = null) {
        $this->filesystem = new Filesystem();
        $this->applicationClass = $applicationClass;
        $this->rootFolder = $rootFolder;
        $this->debug = $debug;
        $this->loggerFactory = $loggerFactory;
    }

    /**
     * Creates an Application instance which is ready to run
     * @param type $applicationClass The application classname
     * @param type $rootFolder The rootfolder where the application ins sitting
     * @param type $debug Wherher to run the application in debug mode
     * @return Application
     */
    public function create() {

        $this->filesystem->assertFolderWritable(
                $this->rootFolder . '/var/cache');

        if ($this->loggerFactory === null) {
            $this->loggerFactory = CommonLoggerFactory::class;
        }


        $this->config = (new ConfigurationFactory($this->rootFolder, $this->debug))
                ->create();

        $this->builLogger();

        /* @var $application \Blend\Framework\Application\Application */

        $args = [
            $this->config,
            $this->logger,
            $this->rootFolder
        ];

        $application = (new \ReflectionClass($this->applicationClass))
                ->newInstanceArgs($args);

        file_put_contents($this->rootFolder
                . '/var/cache/'
                . crc32($this->applicationClass)
                . '.cache'
                , serialize($application));

        return $application;
    }

    /**
     * Build a Logger component.
     */
    private function builLogger() {

        $logFolder = $this->filesystem->assertFolderWritable(
                $this->rootFolder . '/var/log');

        $logLevel = $this->debug === true ? LogLevel::DEBUG : $this->config->get('logger.level', LogLevel::WARNING);

        $args = [
            $logFolder
            , $this->config->get('logger.name', 'application')
            , $this->config->get('logger.filelogger.maxfiles', 10)
            , $logLevel
        ];

        $this->logger = (new \ReflectionClass($this->loggerFactory))
                ->newInstanceArgs($args)
                ->create();

        $this->logger->debug('Logger started');
    }

}
