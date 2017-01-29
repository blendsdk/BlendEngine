<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Routing;

use Symfony\Component\Routing\RouteCollection;

/**
 * RouteBuilder to help create Routes.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class RouteBuilder
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param string       $path         The path pattern to match
     * @param array        $defaults     An array of default parameter values
     * @param array        $requirements An array of requirements for parameters (regexes)
     * @param array        $options      An array of options
     * @param string       $host         The host pattern to match
     * @param string|array $schemes      A required URI scheme or an array of restricted schemes
     * @param string|array $methods      A required HTTP method or an array of restricted methods
     * @param string       $condition    A condition that should evaluate to true for the route to match
     * @param mixed        $name
     */
    public function addRoute($name, $path, array $defaults = array(), array $requirements = array(), array $options = array(), $host = '', $schemes = array(), $methods = array(), $condition = '')
    {
        $this->routes->add($name, new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition));
    }

    /**
     * Adds a Route to the Route Collection.
     *
     * @param type  $name
     * @param type  $path
     * @param array $controlerAction
     * @param array $defaults
     *
     * @return \Blend\Component\Routing\Route
     */
    public function route($name, $path, array $controlerAction, array $defaults = array())
    {
        $params = array_merge($defaults, array(
            RouteAttribute::CONTROLLER => $controlerAction,
        ));
        $route = new Route($path, $params);
        $this->routes->add($name, $route);

        return $route;
    }

    public function redirectRoute($path, $toRouteName, array $controlerAction)
    {
        $this->assertRouteExists($toRouteName);
        $this->route(uniqid(), $path, $controlerAction, array('routeName' => $toRouteName, 'route' => $this->routes->get($toRouteName))
        );
    }

    private function assertRouteExists($routeName)
    {
        if ($this->routes->get($routeName) === null) {
            throw new Exception("Route $routeName is not defined!");
        }
    }
}
