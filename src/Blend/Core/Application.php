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

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;
use Blend\Core\Environments;
use Blend\Core\Services;
use Blend\Core\Configiration;
use Blend\Core\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Blend\Core\JsonToResponseListener;
use Blend\Core\StringToResponseListener;
use Blend\Core\SessionServiceListener;
use Blend\Data\Database;

/**
 * Base class for a BlendEngine application
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Application implements HttpKernelInterface, TerminableInterface {

    /**
     * Holds instantiated service objects
     * @var \ArrayAccess
     */
    private $services;

    /**
     * @var ControllerResolver
     */
    private $controllerResolver;

    /**
     * Points to the root folder of this application
     * @var string
     */
    protected $rootFolder;

    /**
     * Holds the current environment key. it is either production or development
     * @var Environments
     */
    protected $environment;

    /**
     * Holds an array of modules
     * @var \ArrayAccess
     */
    protected $modules;

    /**
     * Holds a collection of routes normally gathered from the modules
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Retuns an array of instantiated Module objects
     */
    protected abstract function getModules();

    public function __construct($environment, $rootFolder) {
        $this->environment = $environment;
        $this->rootFolder = $rootFolder;
        $this->services = array();
        $this->modules = array();
        $this->routes = new RouteCollection();
        $this->registerServices();
        $this->registerModules();
    }

    /**
     * Registers the modules for this application
     */
    protected function registerModules() {
        $this->modules = $this->getModules();
        foreach ($this->modules as $module) {
            $this->routes->addCollection($module->getRoutes());
        }
    }

    /**
     * Regsiters the services for this application
     */
    protected function registerServices() {
        $this->createLoggerService();
        $this->createConfigService();
        $this->createEventDispatcherService();
        $this->createHttpKernelService();
        $this->createDatabaseService();
    }

    /**
     * Retuns the HttpKernel Service
     * @return HttpKernel
     */
    protected function getHttpKernel() {
        return $this->services[Services::HTTP_KERNEL_SERVICE];
    }

    /**
     * Retuns the EventDispatcher service
     * @return EventDispatcher
     */
    protected function getDispatcher() {
        return $this->services[Services::EVENT_DISPATCHER_SERVICE];
    }

    /**
     * Retuns an instance to the Logger service
     * @return \Monolog\Logger
     */
    protected function getLogger() {
        return $this->services[Services::LOGGER_SERVICE];
    }

    /**
     * Retuns the current Requests context
     * @return RequestContext
     */
    protected function getRequestContext() {
        return $this->services[Services::REQUEST_CONTEXT];
    }

    /**
     * Retuns a reference to the \Spot\Locator object
     * @return \Blend\Data\Database
     */
    public function getDatabase() {
        return $this->getService(Services::DATABASE_SERVICE);
    }

    private function createDatabaseService() {
        $dbConfig = $this->getConfig(Configiration::DATABASE_CONFIG);
        $this->registerService(Services::DATABASE_SERVICE, new Database($dbConfig));
    }

    /**
     * Creates the HttpKernel service
     */
    private function createHttpKernelService() {
        $this->controllerResolver = new ControllerResolver($this->getLogger(), $this);
        $httpKernel = new HttpKernel(
                $this->getDispatcher(), $this->controllerResolver
        );
        $this->registerService(Services::REQUEST_CONTEXT, new RequestContext());
        $this->registerService(Services::HTTP_KERNEL_SERVICE, $httpKernel);
        $this->getDispatcher()->addSubscriber(new StringToResponseListener());
        $this->getDispatcher()->addSubscriber(new JsonToResponseListener());
        $this->getDispatcher()->addSubscriber(new SessionServiceListener());
    }

    /**
     * Creates the EventDispatcher service
     */
    private function createEventDispatcherService() {
        $dispatcher = new EventDispatcher();
        $this->registerService(Services::EVENT_DISPATCHER_SERVICE, $dispatcher);
    }

    /**
     * Creates the application configuration service
     */
    private function createConfigService() {
        $config = new Configiration("{$this->rootFolder}/config/{$this->environment}.config.php");
        $this->registerService(Services::CONFIG_SERVICE, $config);
    }

    /**
     * Creates the Logger service
     */
    private function createLoggerService() {
        $logger = new Logger($this->environment);

        if ($this->isProduction()) {
            $logger->pushHandler(
                    new StreamHandler("{$this->rootFolder}/var/logs/{$this->environment}.log", Logger::WARNING)
            );
        } else {
            $logger->pushHandler(
                    new ChromePHPHandler(Logger::DEBUG)
            );
        }
        $this->registerService(Services::LOGGER_SERVICE, $logger);
    }

    /**
     * Handles the incoming http request by matching the request of the registered
     * routs in this application
     * @param Request $request
     * @param type $type
     * @param type $catch
     * @return type
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {
        $urlMatcher = new RedirectableUrlMatcher($this->routes, $this->getRequestContext());
        $urlMatcher->getContext()->fromRequest($request);
        $request->attributes->add($urlMatcher->match($request->getPathInfo()));
        return $this->getHttpKernel()->handle($request, $type, $catch);
    }

    /**
     * Handles the request and delivers the response.
     *
     * @param Request|null $request Request to process
     */
    public function run(Request $request = null) {

        try {
            if (null === $request) {
                $request = Request::createFromGlobals();
            }
            $response = $this->handle($request);
        } catch (ResourceNotFoundException $e) {
            $response = $this->resourceNotFoundResponse($e->getMessage());
        } catch (\Exception $e) {
            $response = $this->fatalExceptionResponse($e->getMessage(), $e);
        }
        $response->send();

        $this->terminate($request, $response);
    }

    /**
     * Creates a fatal exception response (500)
     * @param type $message
     * @param type $exception
     */
    protected function fatalExceptionResponse($message, $exception = null) {
        $msg = $message;
        if ($this->isDevelopment()) {
            $msg = $exception->getMessage();
        }
        $this->getLogger()->error($message);
        return new Response($message, $this->isDevelopment() ? 200 : 500);
    }

    /**
     * Creates a resource not found response (404)
     * @param type $message
     * @return Response
     */
    protected function resourceNotFoundResponse($message) {
        $this->getLogger()->warn($message);
        return new Response($message, 404);
    }

    /**
     * Check if this application is in production mode
     * @return boolean
     */
    protected function isProduction() {
        return $this->environment === Environments::PRODUCTION;
    }

    /**
     * Check if this application is in development mode
     * @return boolean
     */
    protected function isDevelopment() {
        return $this->environment === Environments::DEVELOPMENT;
    }

    /**
     * Handles the application termination using the HttpKernel object
     * @param Request $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response) {
        $this->getHttpKernel()->terminate($request, $response);
    }

    /**
     * Registeres a service in this Application
     * @param type $name
     * @param type $service
     */
    protected function registerService($name, $service) {
        $this->services[$name] = $service;
    }

    /**
     * Retusna a service by its name
     * @param string $name
     * @return object
     */
    public function getService($name) {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        } else {
            return null;
        }
    }

    public function getConfig($name) {
        return $this->getService(Services::CONFIG_SERVICE)->get($name);
    }

}
