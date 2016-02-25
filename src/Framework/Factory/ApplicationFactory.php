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
use Blend\Framework\Application\Application;

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

    /**
     * @var string
     */
    private $rootFolder;

    /**
     * @var boolean
     */
    private $debug;

    /**
     * @var string
     */
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

    /**
     * @var string
     */
    private $cacheFolder;

    /**
     * @var string
     */
    private $appCacheName;

    /**
     * @var boolean
     */
    private $memoryCache;

    /**
     * Creates an Application instance by default injecting the CommonLoggerFactory
     * if none provided to $loggerFactory
     * @param string $applicationClass
     * @param string $rootFolder
     * @param boolean $debug
     * @param string $loggerFactory
     */
    public function __construct($applicationClass, $rootFolder, $debug = false, $loggerFactory = null) {
        $this->filesystem = new Filesystem();
        $this->applicationClass = $applicationClass;
        $this->rootFolder = $rootFolder;
        $this->debug = $debug;
        $this->loggerFactory = $loggerFactory;
        // Set the default factory
        if ($this->loggerFactory === null) {
            $this->loggerFactory = CommonLoggerFactory::class;
        }
        $this->cacheFolder = $rootFolder . '/var/cache';
        $this->appCacheName = $this->cacheFolder
                . '/' . crc32($applicationClass) . '.cache';
        $this->memoryCache = extension_loaded('apcu');
    }

    public function create() {
        if (($application = $this->loadFromCache()) === null) {
            $this->createConfiguration();
            $this->createLogger();
            $application = $this->createApplication();
            $this->saveToCache($application);
        }

        return $application;
    }

    private function saveToCache(Application $application) {
        if (!$this->debug) {
            if ($this->memoryCache) {
                apcu_clear_cache();
                if (apcu_add($this->appCacheName, serialize($application))) {
                    return;
                } else {
                    $this->logger->warning('Unable to cache the application in memory!');
                }
            } else {
                $this->filesystem->assertFolderWritable($this->cacheFolder);
                file_put_contents($this->appCacheName, serialize($application));
            }
        }
    }

    private function loadFromCache() {
        if (!$this->debug) {
            if ($this->memoryCache) {
                if (apcu_exists($this->appCacheName)) {
                    $success = false;
                    $app = apcu_fetch($this->appCacheName, $success);
                    if ($success) {
                        return unserialize($app);
                    }
                }
            } else {
                $this->filesystem->assertFolderWritable($this->cacheFolder);
                if ($this->filesystem->exists($this->appCacheName)) {
                    return unserialize(file_get_contents($this->appCacheName));
                }
            }
        }
        return null;
    }

    private function z_loadFromCache() {
        // Check the cache folder anyways!
        $this->filesystem->assertFolderWritable(
                $this->rootFolder . '/var/cache');
        if ($this->debug === false && $this->filesystem->exists($this->appCacheName)) {
            return unserialize(file_get_contents($this->appCacheName));
        } else {
            return null;
        }
    }

    /**
     * Creates an Application component
     * @return \Blend\Framework\Application\Application
     */
    private function createApplication() {
        $args = [
            $this->config,
            $this->logger,
            $this->rootFolder
        ];
        return (new \ReflectionClass($this->applicationClass))
                        ->newInstanceArgs($args);
    }

    /**
     * Creates a Configuration component
     */
    private function createConfiguration() {
        $this->config = (new ConfigurationFactory($this->rootFolder, $this->debug))
                ->create();
    }

    /**
     * Creates a Logger component
     */
    private function createLogger() {

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
