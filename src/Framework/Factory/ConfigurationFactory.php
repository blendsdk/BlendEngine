<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Factory;

use Blend\Component\Configuration\Configuration;
use Blend\Component\DI\ObjectFactoryInterface;
use Blend\Component\Filesystem\Filesystem;

/**
 * ConfigurationFactory.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ConfigurationFactory implements ObjectFactoryInterface
{
    protected $rootFolder;
    protected $filesystem;
    protected $debug;

    public function __construct($rootFolder, $debug = false)
    {
        $this->rootFolder = $rootFolder;
        $this->debug = $debug;
        $this->filesystem = new Filesystem();
    }

    public function create()
    {
        $configFile = $this->rootFolder.'/config/config.json';
        $cacheFile = $this->rootFolder.'/var/cache/config.cache';
        if ($this->filesystem->exists($cacheFile) && !$this->debug) {
            $config = new Configuration();
            $config->load($cacheFile);
        } else {
            $config = Configuration::createFromFile($configFile);
            $config->dump($cacheFile);
        }
        if (!$config->has('debug')) {
            $config->mergeWith(array('debug' => $this->debug));
        }

        return $config;
    }

    public function getType()
    {
        return Configuration::class;
    }
}
