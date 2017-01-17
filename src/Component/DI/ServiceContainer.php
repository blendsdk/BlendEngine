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

use Composer\Autoload\ClassLoader;

/**
 * ServiceContainer.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ServiceContainer extends Container
{
    public function __construct()
    {
        parent::__construct();
        $this->setScalar(Container::class, $this);
    }

    /**
     * Loads the services from a dictionary array. The array keys should be
     * the interface names and the values should be class names.
     *
     * @param type $services a dictionary array
     */
    public function loadServices($services = [])
    {
        $classLoader = $this->getClassLoader();
        if (!is_array($services)) {
            $services = array($services);
        }
        foreach ($services as $interface => $serviceDescription) {
            if (is_string($serviceDescription)) {
                $this->defineSingletonWithInterface($interface, $serviceDescription);
            } elseif ($classLoader !== null &&
                    is_array($serviceDescription) &&
                    count($serviceDescription) == 2) {
                list($folder, $className) = $serviceDescription;
                $ns = explode('\\', $className);
                $classLoader->addPsr4($ns[0].'\\', $folder);
                $this->defineSingletonWithInterface($interface, $className);
            }
        }
    }

    /**
     * @return ClassLoader
     */
    private function getClassLoader()
    {
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
