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

use Blend\Core\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Route;

/**
 * Base class for all modules in BlendEngine
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Module {

    /**
     * @var Blend\Core\Application
     */
    protected $application;

    protected abstract function createRoutes();

    public function __construct(Application $application) {
        $this->application = $application;
        $this->createRoutes();
    }

    /**
     * Adds a new route to reditrect and old URL to a new URL
     * @param string $name
     * @param string $oldUrl
     * @param string $newUrl
     */
    protected function addRedirectRoute($name, $oldUrl, $newUrl) {
        $this->addRoute($name, new Route($oldUrl, array(
            '_controller' => function() use ($newUrl) {
                return new RedirectResponse($newUrl, 301);
            }
        )));
    }

    /**
     * Adds a new route for this module
     * @param type $name
     * @param Route $route
     */
    protected function addRoute($name, Route $route) {
        $route->setDefault('_module_', $this);
        $this->application->addRoute($name, $route);
    }

}
