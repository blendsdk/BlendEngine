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
use Blend\Component\Cache\LocalCache;
use Blend\Component\Application\Application as BaseApplication;
use Blend\Component\DI\ServiceContainer;
use Blend\Component\Configuration\Configuration;
use Blend\Component\Routing\RouteProviderInterface;
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

    /**
     * @var LocalCache
     */
    protected $localCache;

    public function __construct(Configuration $config
    , LoggerInterface $logger
    , LocalCache $localCache
    , $rootFolder) {

        /**
         * Calling the initialize from the constructor will force some of
         * services to be instantiated early on which will result these object
         * beserialized too when the Application is being cached
         */
        $this->rootFolder = $rootFolder;
        $this->routeCollection = new RouteCollection();
        $this->localCache = $localCache;
        $config->mergeWith(['app.root.folder' => $rootFolder]);
        $this->initialize($logger, $config);
    }

    protected function initialize(LoggerInterface $logger
    , Configuration $config) {

        date_default_timezone_set($config->get('timezone', 'UTC'));
        $this->container = new ServiceContainer();
        $this->container->defineClass(ControllerResolverService::class);
        $this->container->setScalars([
            LoggerInterface::class => $logger,
            Configuration::class => $config,
            LocalCache::class => $this->localCache
        ]);

        if (!$this->container->loadServicesFromFile($this->rootFolder
                        . '/config/services.json')) {
            $logger->notice(
                    "No service description file found!");
        }
    }

    protected function finalize(Request $request, Response $response) {

    }

    protected function handleRequest(Request $request) {
        $routes = $this->collectRoutes();

        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($routes, $context);
        $pathInfo = $request->getPathInfo();
        $parameters = $matcher->match($request->getPathInfo());
        list($controllerName, $action) = $parameters['_controller'];
        $controller = $this->container->get($controllerName);
        return call_user_func_array([$controller, $action], $parameters);
    }

    protected function handleRequestException(\Exception $ex, Request $request) {
        return new Response($ex->getMessage(), 500);
    }

    protected function collectRoutes() {
        return $this->localCache->withCache(__CLASS__ . __FUNCTION__, function() {
                    $collection = new RouteCollection();
                    $services = $this->container->getByInterface(RouteProviderInterface::class);
                    foreach ($services as $service) {
                        $service->loadRoutes($collection);
                    }
                    return $collection;
                });
    }

}
