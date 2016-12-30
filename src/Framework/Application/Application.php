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

use Blend\Component\DI\Container;
use Psr\Log\LoggerInterface;
use Blend\Component\Cache\LocalCache;
use Blend\Component\Application\Application as BaseApplication;
use Blend\Component\DI\ServiceContainer;
use Blend\Component\Configuration\Configuration;
use Blend\Component\Routing\RouteProviderInterface;
use Blend\Component\HttpKernel\Event\GetResponseEvent;
use Blend\Component\HttpKernel\Event\GetExceptionResponseEvent;
use Blend\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Blend\Component\HttpKernel\Event\GetControllerResponseEvent;
use Blend\Component\HttpKernel\Event\GetFinalizeResponseEvent;
use Blend\Component\HttpKernel\ControllerHandler;
use Blend\Component\HttpKernel\ControllerHandlerInterface;
use Blend\Component\Session\SessionProviderInterface;
use Blend\Component\Session\NativeSessionProvider;
use Blend\Component\Filesystem\Filesystem;
use Blend\Component\Routing\RouteBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Blend\Component\Routing\Generator\UrlGenerator;
use Blend\Framework\Support\Runtime\RuntimeProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Blend\Framework\Security\SecurityHandler;
use Blend\Component\Routing\RouteAttribute;
use Blend\Framework\Support\Runtime\RuntimeAttribute;

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

    /**
     * @var Filesystem
     */
    protected $filesystem;

    protected abstract function confiureServices(ServiceContainer $container);

    public function __construct(Configuration $config
    , LoggerInterface $logger
    , LocalCache $localCache
    , $rootFolder) {

        /**
         * Calling the initialize from the constructor will force some of
         * services to be instantiated early on which will result these object
         * be serialized too when the Application is being cached
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
        $this->filesystem = new Filesystem();
        $this->container->setScalars([
            LoggerInterface::class => $this->logger,
            Configuration::class => $config,
            LocalCache::class => $this->localCache,
            EventDispatcherInterface::class => $this->dispatcher,
            Container::class => $this->container,
            Filesystem::class => $this->filesystem,
            RuntimeAttribute::APPLICATION_ROOT_FOLDER => $this->rootFolder,
            RuntimeAttribute::APPLICATION_CACHE_FOLDER => $this->localCache->getCacheFolder(),
            RuntimeAttribute::DEBUG => $config->get('debug', false)
        ]);

        /**
         * Adds the SecurityHandler class by default. This will
         * add a small overhead to the request/response cycle
         * but we gain functionality by having a _authenticated_user
         * when possible
         */
        $this->container->defineSingleton(SecurityHandler::class);
        $this->confiureServices($this->container);
        $this->installEventSubscribers();
    }

    /**
     * Check and install the application specific Runtime object to be recalled
     * and used by its interface name
     */
    protected function checkAndInstallRuntimeProvider() {
        /**
         * Should the application have a customized Runtime environment
         * then we set by its interface name. This creates a duplicate entry
         * in the DI Container since it does not support interface aliases!
         */
        $providers = $this->container
                ->getByInterface(RuntimeProviderInterface::class);
        if (count($providers) === 1) {
            $this->container
                    ->setScalar(RuntimeProviderInterface::class, $providers[0]);
        }
    }

    protected function handleRequest(Request $request) {

        $this->container->setScalar(Request::class, $request);
        $request->attributes->replace($this->matchRequestToRoutes($request));

        $this->initializeSession($request);
        $this->checkAndInstallRuntimeProvider();


        /* @var $event GetResponseEvent */
        $responseEvent = $this->container->get(GetResponseEvent::class);
        $this->dispatcher->dispatch(KernelEvents::REQUEST, $responseEvent);
        if ($responseEvent->hasResponse()) {
            return $responseEvent->getResponse();
        }

        $controllerHandler = $this->getControllerHandler();
        $controllerResponse = $controllerHandler->handle($request);
        if ($controllerResponse instanceof Response) {
            return $controllerResponse;
        }

        /* @var $controllerResposeEvent GetControllerResponseEvent */
        $controllerResposeEvent = $this->container->get(GetControllerResponseEvent::class, [
            'controllerResponse' => $controllerResponse
        ]);
        $this->dispatcher->dispatch(KernelEvents::CONTROLLER_RESPONSE, $controllerResposeEvent);
        if ($controllerResposeEvent->hasResponse()) {
            return $controllerResposeEvent->getResponse();
        }

        return $controllerResponse;
    }

    protected function initializeSession(Request $request) {
        if (!$request->hasSession()) {
            if (!$this->container->isDefined(SessionProviderInterface::class)) {
                $savePath = $this->filesystem->assertFolderWritable($this->rootFolder . '/var/session');
                $this->container->defineSingletonWithInterface(
                        SessionProviderInterface::class
                        , NativeSessionProvider::class
                        , ['save_path' => $savePath]
                );
            }
            /* @var $provider SessionProviderInterface */
            $provider = $this->container->get(SessionProviderInterface::class);
            $provider->initializeSession($request);
            $this->container->setScalar(SessionInterface::class
                    , $provider->getSession());
        }
    }

    protected function finalizeResponse(Response $response) {
        $event = $this->container->get(GetFinalizeResponseEvent::class, [
            'response' => $response
        ]);
        $this->dispatcher->dispatch(KernelEvents::FINALIZE_RESPONSE, $event);
    }

    /**
     * Returns a controller handler Service
     * @return ControllerHandlerInterface
     */
    protected function getControllerHandler() {
        if (!$this->container->isDefined(ControllerHandlerInterface::class)) {
            $this->container->defineSingletonWithInterface(
                    ControllerHandlerInterface::class
                    , ControllerHandler::class);
        }
        return $this->container->get(ControllerHandlerInterface::class);
    }

    /**
     * Prepares the Routing and the UrlGenerator
     * @param Request $request
     * @return type
     */
    protected function prepareRouting(Request $request) {
        $context = new RequestContext();

        $routes = $this->collectRoutes();
        $context->fromRequest($request);

        $this->container->setScalars([
            RouteCollection::class => $routes,
            RequestContext::class => $context,
        ]);

        $this->container->defineSingletonWithInterface(
                UrlGeneratorInterface::class, UrlGenerator::class);

        return [$routes, $context];
    }

    protected function matchRequestToRoutes(Request $request) {
        list($routes, $context) = $this->prepareRouting($request);
        $matcher = new UrlMatcher($routes, $context);
        return $matcher->match($request->getPathInfo());
    }

    protected function handleRequestException(\Exception $ex, Request $request) {

        /* @var $event  GetExceptionResponseEvent */
        $event = $this->container->get(GetExceptionResponseEvent::class, [
            'exception' => $ex
        ]);

        $this->dispatcher->dispatch(KernelEvents::REQUEST_EXCEPTION, $event);
        if ($event->hasResponse()) {
            $response = $event->getResponse();
        } else if ($request->attributes->get(RouteAttribute::JSON_RESPONSE, false)) {
            $response = $this->createJSONExceptionResponse($ex);
        } else {
            $response = new Response($ex->getMessage(), 500);
        }
        $this->logger->error($ex->getMessage(), $ex->getTrace());
        $this->logger->debug($ex->getMessage(), $ex->getTrace());
        return $response;
    }

    /**
     * Create and return a JsonResponse for an Exception
     * @param \Exception $ex
     * @return JsonResponse
     */
    protected function createJSONExceptionResponse(\Exception $ex) {
        return new JsonResponse([
            'message' => $ex->getMessage(),
            'code' => $ex->getCode(),
            'exception' => true,
            'exceptionTyle' => get_class($ex)
                ], 500);
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
                    $routes = new RouteCollection();
                    $routeBuilder = new RouteBuilder($routes);
                    $services = $this->container->getByInterface(RouteProviderInterface::class);
                    foreach ($services as $service) {
                        $service->loadRoutes($routeBuilder);
                    }
                    return $routeBuilder->getRoutes();
                });
    }

}
