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

    /**
     * Reference to there this module is located on the filesystem
     * @var type
     */
    protected $path;

    protected abstract function createRoutes();

    /**
     * Set the path of this module. This funcion is called when the controller
     * is resolved
     * @param string $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

    public function getPath($append = '') {
        return $this->path . $append;
    }

    public function __construct(Application $application) {
        $this->application = $application;
        $this->initServices();
        $this->createRoutes();
    }

    /**
     * This method is called by the ctor to provide an option to create/initialize
     * module specific services. For example a database service
     * @return type
     */
    protected function initServices() {
        return null;
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
        if (is_null($route->getDefault('_locale'))) {
            $route->setDefault('_locale', $this->application->getLocale());
        }
        $this->application->addRoute($name, $route);
    }

    protected function addSecuredRoute($name, $path, $controllerAction, $defaults = array()) {
        $secured = array(
            'secure' => true
        );
        $this->addSimpleRoute($name, $path, $controllerAction, array_replace($secured, $defaults));
    }

    protected function addSimpleRoute($name, $path, $controllerAction, $defaults = array()) {
        $routeDefaults = array_replace($defaults, array(
            '_controller' => $controllerAction
        ));
        $this->addRoute($name, new Route($path, $routeDefaults));
    }

}
