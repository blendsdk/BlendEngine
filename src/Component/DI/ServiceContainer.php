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
        if (file_exists($filename)) {
            $config = json_decode(file_get_contents($filename), true);
            foreach ($config as $interface => $serviceDescription) {
                if (is_string($serviceDescription)) {
                    $this->defineSingletonWithInterface($interface
                            , $serviceDescription);
                }
            }
            return true;
        }
        return false;
    }

}
