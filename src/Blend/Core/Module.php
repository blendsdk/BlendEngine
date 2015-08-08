<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Core;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Blend\Core\Application;

/**
 * Base class for all modules in BlendEngine
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Module {

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var Blend\Core\Application
     */
    protected $application;

    protected abstract function createRoutes();

    public function __construct(Application $application) {
        $this->application = $application;
        $this->routes = new RouteCollection();
        $this->createRoutes();
    }

    public function getRoutes() {
        return $this->routes;
    }

}
