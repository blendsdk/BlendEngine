<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Support;

use Blend\Component\DI\Container;
use Blend\Framework\Support\RuntimeProviderInterface;

/**
 * Runtime is essencially a wrapper around the Container that can be used
 * to be customized and passed round to get and set domain (project)
 * specific settings
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Runtime implements RuntimeProviderInterface {

    /**
     * @var Container 
     */
    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function getAppRootFolder() {
        return $this->get('_app_root_folder');
    }

    public function getAppCacheFolder() {
        return $this->get('_app_cache_folder');
    }

    public function set($key, $value) {
        $this->container->setScalar($key, $value);
    }

    public function get($key, $default = null) {
        if ($this->container->isDefined($key)) {
            return $this->container->get($key);
        } else {
            return $default;
        }
    }

}
