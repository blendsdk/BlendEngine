<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Application;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Blend\Component\Application\Application as BaseApplication;
use Blend\Component\DI\ServiceContainer;
use Blend\Component\Configuration\Configuration;
use Blend\Component\Routing\RouteProvidesInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Application extends BaseApplication {

    /**
     * @var ServiceContainer
     */
    protected $container;

    /**
     * @var string
     */
    protected $rootFolder;

    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    public function __construct(Configuration $config
    , LoggerInterface $logger
    , $rootFolder) {

        /**
         * Calling the initialize from the constructor will force some of
         * services to be instantiated early on which will result these object
         * beserialized too when the Application is being cached
         */
        $this->rootFolder = $rootFolder;
        $this->routeCollection = new RouteCollection();
        $config->mergeWith(['app.root.folder' => $rootFolder]);
        $this->initialize($logger, $config);
    }

    protected function initialize(LoggerInterface $logger
    , Configuration $config) {

        date_default_timezone_set($config->get('timezone', 'UTC'));
        $this->container = new ServiceContainer();
        $this->container->setScalars([
            LoggerInterface::class => $logger,
            Configuration::class => $config
        ]);

        if (!$this->container->loadServicesFromFile($this->rootFolder
                        . '/config/services.json')) {
            $logger->notice(
                    "No service description file found!");
        }
    }

    protected function finalize(Request $request, Response $response) {
        //
    }

    protected function handleRequest(Request $request) {
        $this->getRoutes();
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($this->routeCollection, $context);
        $pathInfo = $request->getPathInfo();
        $parameters = $matcher->match($request->getPathInfo());
        list($controllerName, $action) = $parameters['_controller'];
        $controller = $this->container->get($controllerName);
        return call_user_func_array([$controller, $action], $parameters);
    }

    protected function handleRequestException(\Exception $ex, Request $request) {
        return new Response($ex->getMessage(), 500);
    }

    protected function getRoutes() {
        $services = $this->container->getByInterface(RouteProvidesInterface::class);
        foreach ($services as $service) {
            $service->loadRoutes($this->routeCollection);
        }
    }

}
