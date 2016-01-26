<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Dependency;

use ReflectionClass;
use Blend\Component\InvalidConfigException;

/**
 * This class provides dependecy injection container functionality.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Container {

    protected $container;

    public function __construct() {
        $this->container = [];
    }

}
