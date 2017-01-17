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

use Blend\Component\Cache\LocalCache;
use Blend\Component\Configuration\Configuration;
use Blend\Component\DI\ObjectFactoryInterface;
use Blend\Component\Filesystem\Filesystem;
use Blend\Framework\Application\Application;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * ApplicationFactory creates an Application instance. By default
 * the a Application will be instantiated with a RotatingFile logger that is
 * instantiated by CommonLoggerFactory. You can set a differect Logger by
 * implementing (or overriding CommonLoggerFactory)
 * a new LoggerFactoryInterface and passing it to setLoggerFactory.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ApplicationFactory implements ObjectFactoryInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $rootFolder;

    /**
     * @var bool
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
     * @var LocalCache
     */
    private $localCache;

    /**
     * Creates an Application instance by default injecting the CommonLoggerFactory
     * if none provided to $loggerFactory.
     *
     * @param string $applicationClass
     * @param string $rootFolder
     * @param bool   $debug
     * @param string $loggerFactory
     */
    public function __construct($applicationClass, $rootFolder, $debug = false, $loggerFactory = null)
    {
        $this->filesystem = new Filesystem();
        $this->applicationClass = $applicationClass;
        $this->rootFolder = realpath($rootFolder);
        $this->debug = $debug;
        $this->loggerFactory = $loggerFactory;
        // Set the default factory
        if ($this->loggerFactory === null) {
            $this->loggerFactory = CommonLoggerFactory::class;
        }
    }

    public function create()
    {
        $this->createConfiguration();
        $this->createLogger();
        $this->createLocalCache();

        return $this->createApplication();
    }

    /**
     * Creates an Application component.
     *
     * @return \Blend\Framework\Application\Application
     */
    private function createApplication()
    {
        $args = [
            $this->config,
            $this->logger,
            $this->localCache,
            $this->rootFolder,
        ];

        return (new \ReflectionClass($this->applicationClass))
                        ->newInstanceArgs($args);
    }

    /**
     * Create a local cache provider.
     */
    private function createLocalCache()
    {
        $cacheFolder = $this->rootFolder.'/var/cache';
        $this->filesystem->assertFolderWritable($cacheFolder);
        $this->localCache = new LocalCache($cacheFolder, $this->logger, $this->debug);
    }

    /**
     * Creates a Configuration component.
     */
    private function createConfiguration()
    {
        $this->config = (new ConfigurationFactory($this->rootFolder, $this->debug))
                ->create();
    }

    /**
     * Creates a Logger component.
     */
    private function createLogger()
    {
        $logFolder = $this->filesystem->assertFolderWritable(
                $this->rootFolder.'/var/log');

        $logLevel = $this->debug === true ? LogLevel::DEBUG : $this->config->get('logger.level', LogLevel::WARNING);

        $args = [
            $logFolder, $this->config->get('logger.name', 'application'), $this->config->get('logger.filelogger.maxfiles', 10), $logLevel,
        ];

        $this->logger = (new \ReflectionClass($this->loggerFactory))
                ->newInstanceArgs($args)
                ->create();

        $this->logger->debug('Logger started');
    }
}
