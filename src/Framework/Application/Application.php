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

use Blend\Component\Application\Application as BaseApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Blend\Component\Exception\InvalidConfigException;
use Blend\Component\Configuration\Configuration;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

/**
 * Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Application extends BaseApplication {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $rootFolder;

    /**
     * @var boolean
     */
    protected $debug;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct($rootFolder) {
        parent::__construct();
        $this->rootFolder = $rootFolder;
    }

    protected function checkGetFolder($folder) {
        if (!is_dir($folder) && !is_writable($folder)) {
            throw new InvalidConfigException(
            "$folder does not exist or it is not writable"
            , 500);
        }
        return $folder;
    }

    protected function finalize(Request $request, Response $response) {
        
    }

    protected function handleRequest(Request $request) {
        
    }

    protected function handleRequestException(\Exception $ex, Request $request) {
        
    }

    /**
     * Loads the application configuration file and caches it in the case
     * does not exist. The configuration object (Configuration::class) is then 
     * registered as singleton the the application container
     * @throws InvalidConfigException
     */
    protected function loadConfiguration() {

        $configFile = $this->rootFolder . '/config/config.php';
        if (!file_exists($configFile)) {
            throw new InvalidConfigException("Missing the configiration file: "
            . $configFile, 500);
        }

        $cacheFile = $this->checkGetFolder($this->rootFolder . '/var/cache')
                . '/config.cache';

        if (file_exists($cacheFile)) {
            $config = new Configuration();
            $config->load($cacheFile);
        } else {
            $config = Configuration::createFromFile($configFile);
            $config->dump($cacheFile);
        }

        $this->debug = $config->get('debug', $this->debug);
        $this->config = $config;

        $this->container->singleton(Configuration::class, [
            'factory' => function() use ($config) {
                return $config;
            }
        ]);
    }

    /**
     * Overridable function to setup custom logger handlers.
     * By default the Application will create a rotating file logger
     * @param Logger $logger
     * @param int $defaultLevel
     */
    protected function setupLoggerHandlers(Logger $logger, $defaultLevel) {
        $this->setupRotatingFileLogger($logger, $defaultLevel);
    }

    /**
     * Creates a rotaing file logger handler. The number of files to keep
     * is read fon the application config ogger.filelog.maxfiles = 10
     * @param Logger $logger
     * @param type $level
     */
    protected function setupRotatingFileLogger(Logger $logger, $level) {

        $logFile = $this->checkGetFolder($this->rootFolder . '/var/log')
                . '/' . strtolower($this->name) . '.log';

        $fileHandler = new RotatingFileHandler(
                $logFile, $this->config->get('logger.filelog.maxfiles', 10), $level);
        $logger->pushHandler($fileHandler);
    }

    /**
     * Initializes the logger for this application
     */
    private function createLogger() {
        $logger = new Logger($this->name);
        $defaultLevel = $this->debug === false ? Logger::WARNING : Logger::DEBUG;
        $this->setupLoggerHandlers($logger, $defaultLevel);
        $logger->debug('Logger started!');

        $this->container->singleton(Logger::class, [
            'factory' => function() use($logger) {
                return $logger;
            }
        ]);
    }

    protected function initialize(Request $request) {
        $this->loadConfiguration();
        $this->createLogger();
    }

}
