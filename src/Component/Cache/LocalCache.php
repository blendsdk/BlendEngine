<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Cache;

use Blend\Component\Exception\InvalidConfigException;
use Blend\Component\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

/**
 * LocalCache provides a caching mechanism to be used for caching inline
 * for data structure builting operations. This class either caches the
 * data to the [APP]/var/cache folder or if the APCU module is enabled it
 * will cache the database in the APCU shared memeory.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class LocalCache
{
    /**
     * @var string
     */
    protected $cacheFolder;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var bool
     */
    protected $memoryCache;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct($cacheFolder, LoggerInterface $logger, $debug = false)
    {
        $this->cacheFolder = $cacheFolder;
        $this->memoryCache = extension_loaded('acpu');
        $this->filesystem = new Filesystem();
        $this->debug = $debug;
        $this->logger = $logger;
        if (!$this->filesystem->exists($cacheFolder)) {
            throw new InvalidConfigException(
            "The cache folder does not exist! [$cacheFolder]");
        }
    }

    /**
     * Given a $name/key, this method will either write the returl of the
     * $callback to the cache or read the already saved data from the cache.
     *
     * @param type                            $name
     * @param \Blend\Component\Cache\callable $callback
     *
     * @return mixed
     */
    public function withCache($name, callable $callback)
    {
        if (isset($_SERVER) && isset($_SERVER['REQUEST_URI'])) {
            $name = $name.$_SERVER['REQUEST_URI'];
        }

        if ($this->debug === false) {
            $cacheFile = $this->cacheFolder.'/'.crc32($name).'.cache';
            if ($this->memoryCache === true) {
                return $this->withMemory($cacheFile, $callback);
            } else {
                return $this->withFile($cacheFile, $callback);
            }
        } else {
            return call_user_func($callback);
        }
    }

    /**
     * Try to cache the callback result on disk.
     *
     * @param type                            $cacheFile
     * @param \Blend\Component\Cache\callable $callback
     *
     * @return mixed
     */
    private function withFile($cacheFile, callable $callback)
    {
        if ($this->filesystem->exists($cacheFile)) {
            return unserialize(file_get_contents($cacheFile));
        } else {
            $result = call_user_func($callback);
            file_put_contents($cacheFile, serialize($result));

            return $result;
        }
    }

    /**
     * Try to cache the callback result in APCU.
     *
     * @param type                            $name
     * @param \Blend\Component\Cache\callable $callback
     *
     * @return mixed
     */
    private function withMemory($name, callable $callback)
    {
        if (apcu_exists($name)) {
            $success = false;
            $result = apcu_fetch($name, $success);
            if ($success) {
                return $result;
            } else {
                $this->logger->warning('APCU cache did not return correctly!', array(
                    'cache' => $name,
                ));

                return call_user_func($callback);
            }
        } else {
            $result = call_user_func($callback);
            if (!apcu_add($name, $result)) {
                $this->logger->warning('Unable to store data in cache!', array(
                    'data' => $result,
                ));
            }

            return $result;
        }
    }

    /**
     * Returns the path to the cache folder.
     */
    public function getCacheFolder()
    {
        return $this->cacheFolder;
    }
}
