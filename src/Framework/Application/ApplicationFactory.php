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
 * Description of ApplicationFactory
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
     * @var LoggerFactoryInterface 
     */
    private $loggerFactory;

    public function __construct() {
        $this->filesystem = new Filesystem();
        $this->loggerFactory = null;
    }

    public function setLoggerFactory(LoggerFactoryInterface $loggerFactory) {
        $this->loggerFactory = $loggerFactory;
    }

    /**
     * @param type $applicationClass
     * @param type $rootFolder
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

        $this->container->singleton(Filesystem::class, [
            'factory' => function() {
                return $this->filesystem;
            }
        ]);


        /* @var $application \Blend\Framework\Application\Application */
        $application = $this->container->get($applicationClass, [
            'rootFolder' => $rootFolder
        ]);

        return $application;
    }

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
    }

    private function buildConfigObject() {
        $configFile = $this->rootFolder . '/config/config.php';
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
