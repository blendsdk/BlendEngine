<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Application;

use Blend\Component\DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Blend\Component\Configuration\Configuration;
use Blend\Component\Filesystem\Filesystem;
use Blend\Framework\Logger\CommonLoggerFactory;
use Blend\Framework\Logger\LoggerFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * ApplicationFactory creates an Application instance. By default
 * the a Application will be instantiated with a RotatingFile logger that is
 * instantiated by CommonLoggerFactory. You can set a differect Logger by
 * implementing (or overriding CommonLoggerFactory)
 * a new LoggerFactoryInterface and passing it to setLoggerFactory
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ApplicationFactory {

    private $container;

    /**
     * @var Filesystem
     */
    private $filesystem;
    private $rootFolder;
    private $cacheFolder;
    private $debug;

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

    public function __construct() {
        $this->filesystem = new Filesystem();
        $this->loggerFactory = null;
    }

    /**
     * Sets the LOggerFactory class name to be used when a Logger is created
     * for this Application
     * @param LoggerFactoryInterface $loggerFactory
     */
    public function setLoggerFactory(LoggerFactoryInterface $loggerFactory) {
        $this->loggerFactory = $loggerFactory;
    }

    /**
     * Creates an Application instance which is ready to run
     * @param type $applicationClass The application classname
     * @param type $rootFolder The rootfolder where the application ins sitting
     * @param type $debug Wherher to run the application in debug mode
     * @return Application
     */
    public function create($applicationClass, $rootFolder, $debug = false) {

        $this->container = new Container();
        $this->debug = $debug;
        $this->rootFolder = $rootFolder;
        $this->cacheFolder = $this->filesystem->assertFolderWritable(
                $rootFolder . '/var/cache');

        $this->buildConfigObject();
        $this->builLogger();

        /* @var $application \Blend\Framework\Application\Application */
        $application = $this->container->get($applicationClass, [
            'rootFolder' => $rootFolder
        ]);

        return $application;
    }

    /**
     * Build a Logger component.
     */
    private function builLogger() {
        if ($this->loggerFactory === null) {
            $logFolder = $this->filesystem->assertFolderWritable(
                    $this->rootFolder . '/var/log');
            $this->loggerFactory = new CommonLoggerFactory(
                    $logFolder
                    , $this->config->get('logger.name', 'application')
                    , $this->config->get('logger.filelogger.maxfiles', 10)
            );
        }

        $logLevel = $this->debug === true ? LogLevel::DEBUG : $this->config->get('logger.level', LogLevel::WARNING);
        $logger = $this->loggerFactory->buildLogger($logLevel);
        $logger->debug('Logger started');
        $this->container->singleton(LoggerInterface::class, [
            'factory' => function() use($logger) {
                return $logger;
            }
        ]);
        $this->logger = $logger;
    }

    /**
     * Builds a Configuration component
     */
    private function buildConfigObject() {
        $configFile = $this->rootFolder . '/config/config.json';
        $cacheFile = $this->cacheFolder . '/config.cache';
        if ($this->filesystem->exists($cacheFile) && !$this->debug) {
            $config = new Configuration();
            $config->load($cacheFile);
        } else {
            $config = Configuration::createFromFile($configFile);
            $config->dump($cacheFile);
        }
        $this->container->singleton(Configuration::class, [
            'factory' => function() use($config) {
                return $config;
            }
        ]);
        $this->config = $config;
    }

}
