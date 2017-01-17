<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Configuration;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * The Configuration class provides an option to read the application
 * configuration parameters In BlendEngine. This class also will look
 * for a file called the ".env.json" which can be used to overwrite the
 * configuration parameters with environment specific values.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Configuration
{
    /**
     * Holds the list of paremeters.
     *
     * @var \ArrayAccess
     */
    private $params;

    /**
     * Retuns a parameters value of null of the parameter does not exist.
     *
     * @param string $name
     *
     * @return object|null
     */
    public function get($name, $default = null)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        } else {
            return $default;
        }
    }

    /**
     * Checks if the given name exists in this configuration.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->params);
    }

    public function __construct(array $configArray = [])
    {
        $this->params = [];
        $this->flatten_config($configArray, $this->params);
    }

    /**
     * Mergs the current configuration with another array.
     *
     * @param type $configArray
     */
    public function mergeWith($configArray)
    {
        $all = [];
        $this->flatten_config($configArray, $all);
        $this->params = array_merge($this->params, $all);
    }

    /**
     * Factory method for creating a configution for a PHP file and
     * optionally an .env.json file.
     *
     * @param type $filename
     *
     * @return \Blend\Component\Configuration\Configuration
     *
     * @throws FileNotFoundException
     */
    public static function createFromFile($filename)
    {
        if (file_exists($filename)) {
            $params = json_decode(file_get_contents($filename), true);
            $config = new self($params);
            $envfile = dirname($filename).'/.env.json';
            if (file_exists($envfile)) {
                $envparams = json_decode(file_get_contents($envfile), true);
                $config->mergeWith($envparams);
            }

            return $config;
        } else {
            throw new FileNotFoundException($filename, 500);
        }
    }

    /**
     * Flattens the oprovided array.
     *
     * @param type   $data
     * @param type   $all
     * @param string $lastkey
     */
    protected function flatten_config($data, &$all, $lastkey = '')
    {
        if (is_array_assoc($data)) {
            if (!empty($lastkey)) {
                $lastkey = $lastkey.'.';
            }

            foreach ($data as $key => $value) {
                $this->flatten_config($value, $all, $lastkey.$key);
            }
        } else {
            $all[$lastkey] = $data;
        }
    }

    /**
     * Load a previously dumped configuration parameters.
     *
     * @param array $data
     */
    public function load($dumpFile)
    {
        $this->params = unserialize(file_get_contents($dumpFile));
    }

    /**
     * Dumps the current parameters to a file.
     *
     * @param type $dumpFile
     */
    public function dump($dumpFile)
    {
        file_put_contents($dumpFile, serialize($this->params));
    }
}
