<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Cache;

use Psr\Log\LoggerInterface;
use Blend\Component\Filesystem\Filesystem;
use Blend\Component\Exception\InvalidConfigException;

/**
 * Description of LocalCache
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class LocalCache {

    protected $cacheFolder;
    protected $memoryCache;
    protected $filesystem;
    protected $debug;
    protected $logger;

    public function __construct($cacheFolder, LoggerInterface $logger, $debug = false) {
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

    public function withCache($name, callable $callback) {
        if ($this->debug === false) {
            $cacheFile = $this->cacheFolder . '/' . crc32($name) . '.cache';
            if ($this->memoryCache === true) {
                return $this->withMemory($cacheFile, $callback);
            } else {
                return $this->withFile($cacheFile, $callback);
            }
        } else {
            return call_user_func($callback);
        }
    }

    private function withFile($cacheFile, callable $callback) {
        if ($this->filesystem->exists($cacheFile)) {
            return unserialize(file_get_contents($cacheFile));
        } else {
            $result = call_user_func($callback);
            file_put_contents($cacheFile, serialize($result));
            return $result;
        }
    }

    private function withMemory($name, callable $callback) {
        if (apcu_exists($name)) {
            $success = false;
            $result = apcu_fetch($name, $success);
            if ($success) {
                return $result;
            } else {
                $this->logger->warning('APCU cache did not return correctly!', [
                    'cache' => $name
                ]);
                return call_user_func($callback);
            }
        } else {
            $result = call_user_func($callback);
            if (!apcu_add($name, $result)) {
                $this->logger->warning("Unable to store data in cache!", [
                    'data' => $result
                ]);
            }
            return $result;
        }
    }

}
