<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\DI;

use Blend\Component\DI\Container;
use Composer\Autoload\ClassLoader;

/**
 * ServiceContainer
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ServiceContainer extends Container {

    /**
     * Load services from a JSON configuration. The services will be registered
     * as singletons inside the container
     * @param type $filename
     * @return boolean
     */
    public function loadServicesFromFile($filename) {
        $classLoader = $this->getClassLoader();
        if (file_exists($filename)) {
            $config = json_decode(file_get_contents($filename), true);
            foreach ($config as $interface => $serviceDescription) {
                if (is_string($serviceDescription)) {
                    $this->defineSingletonWithInterface($interface
                            , $serviceDescription);
                } else if ($classLoader !== null &&
                        is_array($serviceDescription) &&
                        count($serviceDescription) == 2) {
                    list($folder, $className) = $serviceDescription;
                    $ns = explode('\\', $className);
                    $classLoader->addPsr4($ns[0] . '\\', $folder);
                    $this->defineSingletonWithInterface($interface
                            , $className);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @return ClassLoader
     */
    private function getClassLoader() {
        $category = spl_autoload_functions();
        foreach ($category as $splLoaders) {
            foreach ($splLoaders as $loader) {
                if ($loader instanceof ClassLoader) {
                    return $loader;
                }
            }
        }
        return null;
    }

}
