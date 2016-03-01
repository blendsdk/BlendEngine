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
use Blend\Component\Cache\LocalCache;
use Blend\Component\Application\Application as BaseApplication;
use Blend\Component\DI\ServiceContainer;
use Blend\Component\Configuration\Configuration;
use Blend\Component\Routing\RouteProviderInterface;
use Blend\Component\HttpKernel\Event\GetResponseEvent;
use Blend\Component\HttpKernel\Event\GetExceptionResponseEvent;
use Blend\Component\DI\Container;
use Blend\Component\HttpKernel\KernelEvents;
use Blend\Framework\Service\ControllerResolverService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
        $this->logger = $logger;
        $config->mergeWith(['app.root.folder' => $rootFolder]);
        $this->initialize($config);
    }

    protected function initialize(Configuration $config) {

        date_default_timezone_set($config->get('timezone', 'UTC'));
        $this->container = new ServiceContainer();
        $this->dispatcher = new EventDispatcher();
        $this->container->setScalars([
            LoggerInterface::class => $this->logger,
            Configuration::class => $config,
            LocalCache::class => $this->localCache,
            EventDispatcher::class => $this->dispatcher,
            Container::class => $this->container
        ]);

        if (!$this->container->loadServicesFromFile($this->rootFolder
                        . '/config/services.json')) {
            $this->logger->notice(
                    "No service description file found!");
        }
        $this->installEventSubscribers();
    }

    protected function finalize(Request $request, Response $response) {

    }

    protected function handleRequest(Request $request) {
        $this->container->setScalar(Request::class, $request);
        $this->matchRequestToRoutes($request);

        //$this->container->setScalar(Request::class, $request);
//        /* @var $event GetResponseEvent */
//        $responseEvent = $this->container->get(GetResponseEvent::class);
//        $this->dispatcher->dispatch(KernelEvents::REQUEST, $responseEvent);
//
//        if($event->hasResponse()) {
//            return $event->getResponse();
//        }
//
//        /* @var $resolver ControllerResolverService */
//        $resolver = $this->container->get(ControllerResolverService::class);
//        $controller = $r
//        try {
//            $result = $resolver->runController();
//            if($result instanceof Response) {
//                return $result;
//            } else {
//                $
//            }
//        } catch (Exception $ex) {
//
//        }
//
//        $routeData = $this->matchRequestToRoutes($request);
//        $routes = $this->collectRoutes();
//
//        $context = new RequestContext();
//        $context->fromRequest($request);
//        $matcher = new UrlMatcher($routes, $context);
//        $pathInfo = $request->getPathInfo();
//        $parameters = $matcher->match($request->getPathInfo());
//        list($controllerName, $action) = $parameters['_controller'];
//        $controller = $this->container->get($controllerName);
//        return call_user_func_array([$controller, $action], $parameters);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function matchRequestToRoutes(Request $request) {
        $routes = $this->collectRoutes();
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($routes, $context);
        $result = $matcher->match($request->getPathInfo());
    }

    protected function handleRequestException(\Exception $ex, Request $request) {

        /* @var $event  GetExceptionResponseEvent */
        $event = $this->container->get(GetExceptionResponseEvent::class, [
            'exception' => $ex
        ]);

        $this->dispatcher->dispatch(KernelEvents::REQUEST_EXCEPTION, $event);
        if ($event->hasResponse()) {
            $response = $event->getResponse();
        } else {
            $response = new Response($ex->getMessage(), 500);
        }
        $this->logger->error($ex->getMessage(), $ex->getTrace());
        return $response;
    }

    /**
     * Find and install the EventSubscribers
     */
    protected function installEventSubscribers() {
        $subscribers = $this->container->getByInterface(EventSubscriberInterface::class);
        foreach ($subscribers as $subscriber) {
            $this->dispatcher->addSubscriber($subscriber);
        }
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
