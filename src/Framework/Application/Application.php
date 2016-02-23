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

/**
 * Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Application extends BaseApplication {

    private $rootFolder;

    public function __construct($rootFolder) {
        parent::__construct();
        $this->rootFolder = $rootFolder;
    }

    protected function checkGetCacheFolder() {
        $cacheFolder = $this->rootFolder . '/var/cache';
        if (!is_dir($cacheFolder) && !is_writable($cacheFolder)) {
            throw new InvalidConfigException(
            "$cacheFolder does not exist or it is not writable"
            , 500);
        }
        return $cacheFolder;
    }

    protected function finalize(Request $request, Response $response) {
        
    }

    protected function handleRequest(Request $request) {
        
    }

    protected function handleRequestException(\Exception $ex, Request $request) {
        
    }

    /**
     * Loads the application configuration file and caches it if the case 
     * does not exist
     * @throws InvalidConfigException
     */
    protected function loadConfiguration() {

        $configFile = $this->rootFolder . '/config/config.php';
        if (!file_exists($configFile)) {
            throw new InvalidConfigException("Missing the configiration file: "
            . $configFile, 500);
        }

        $cacheFile = $this->checkGetCacheFolder($this->rootFolder) 
                . '/config.cache';
        
        if(file_exists($cacheFile)) {
            $config = new Configuration();
            $config->load($cacheFile);
        } else {
            $config = Configuration::createFromFile($configFile);
            $config->dump($cacheFile);
        }
        
        $this->container->singleton(Configuration::class,[
            'factory' => function() use ($config) {
                return $config;
            }
        ]);
        
    }

    protected function initialize(Request $request) {
        $this->loadConfiguration();
    }

}
