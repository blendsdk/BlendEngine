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
use Blend\Core\Environments;
use Blend\Core\Services;
use Blend\Core\Configiration;
use Blend\Core\ControllerResolver;
use Blend\Core\JsonToResponseListener;
use Blend\Core\StringToResponseListener;
use Blend\Core\SessionServiceListener;
use Blend\Core\StaticResourceListener;
use Blend\Core\LocaleServiceListener;
use Blend\Database\Database;
use Blend\Security\SecurityServiceListener;
use Blend\Security\IUser;
use Blend\Translation\Translator;
use Blend\Mail\MailerService;
use Blend\PDF\PDFPrinterService;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Blend\Database\DatabaseQueryException;

/**
 * Base class for a BlendEngine application
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Application implements HttpKernelInterface, TerminableInterface {

    /**
     * Holds the value of the current locale
     * @var type
     */
    private $locale;

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
     * Holds the name of this application
     * @var string
     */
    protected $name;

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
     * Holds an instance of the current (authenticated) user
     * @var User
     */
    protected $user;

    /**
     * Holds a reference to the current Request
     * @var Request
     */
    protected $request;

    /**
     * Retuns an array of instantiated Module objects
     */
    protected abstract function getModules();

    public function __construct($rootFolder, $name, $environment = Environments::PRODUCTION) {
        $this->name = $name;
        $this->environment = $environment;
        $this->rootFolder = $rootFolder;
        $this->services = array();
        $this->modules = array();
        $this->routes = new RouteCollection();
    }

    /**
     * Retuns the application namee
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Retuns the root folder of this application;
     * @return string
     */
    public function getRootFolder($append = '') {
        return $this->rootFolder . $append;
    }

    /**
     * Gets the current locale set for this application by LocaleServiceListener
     * @return type
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Sets the locale for this application. This function is called
     * from LocaleServiceListener
     * @param type $locale
     */
    public function setLocale($locale) {
        $this->locale = $locale;
    }

    /**
     * Retrives the list of registered routes
     * @return RouteCollection
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Adds a Route to this Application
     * @param string $name
     * @param Route $route
     */
    public function addRoute($name, Route $route) {
        $this->routes->add($name, $route);
    }

    /**
     * Retrives the current user
     * @return IUser
     */
    public function getUser() {
        return $this->request->getSession()->get(SecurityServiceListener::SEC_AUTHENTICATED_USER);
    }

    /**
     * Sets the current (authenticated) user for this application. The User is
     * set by the SecurityServiceListener
     * @param User $user
     */
    public function setUser(IUser $user) {
        $user->password = null;
        $this->request->getSession()->set(SecurityServiceListener::SEC_AUTHENTICATED_USER, $user);
    }

    /**
     * Registers the modules for this application
     */
    protected function registerModules() {
        $this->modules = $this->getModules();
    }

    /**
     * Regsiters the services for this application
     */
    protected function registerServices() {

        /**
         * The following services are Layze loaded:
         *  Database, MailerService, UrlGenerator, PDFPrinterService
         */
        $this->createLoggerService();
        $this->createConfigService();
        $this->createEventDispatcherService();
        $this->createTranslationService();
        $this->createHttpKernelService();
        $this->createUrlGeneratorService();
        $this->createUrlMatcherService();
        $this->createTranslationService();
    }

    /**
     * Retuns an instance of the PDFPrinterService
     * @return PDFPrinterService
     */
    public function getPDFPrinter() {
        if (!isset($this->services[Services::PDF_PRINTER_SERVICE])) {
            $this->createPDFPrinterService();
        }
        return $this->services[Services::PDF_PRINTER_SERVICE];
    }

    /**
     * Retuns an instance of the Swift_Mailer
     * @return \Swift_Mailer
     */
    public function getMailer() {
        if (!isset($this->services[Services::EMAIL_SERVICE])) {
            $this->createMailerService();
        }
        return $this->services[Services::EMAIL_SERVICE]->getMailer();
    }

    /**
     * Retuns the UrlGenerator service
     * @return UrlGenerator
     */
    public function getUrlGenerator() {
        if (!isset($this->services[Services::URL_GENERATOR_SERVICE])) {
            $this->createUrlGeneratorService();
        }
        return $this->services[Services::URL_GENERATOR_SERVICE];
    }

    public function trans($id, array $parameters = array(), $domain = null, $locale = null) {
        return $this->getTranslator()->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Generates a url using the UrlGenerator
     * @return UrlGenerator
     */
    public function generateUrl($name, $parameters = array(), $referenceType = UrlGenerator::ABSOLUTE_PATH) {
        $params = array_replace(array('_locale' => $this->getLocale()), $parameters);
        return $this->getUrlGenerator()->generate($name, $params, $referenceType);
    }

    /**
     * Returns the UrlMatcher service
     * @return UrlMatcher
     */
    public function getUrlMatcher() {
        return $this->getService(Services::URL_MATCHER_SERVICE);
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
    public function getDispatcher() {
        return $this->services[Services::EVENT_DISPATCHER_SERVICE];
    }

    /**
     * Retuns an instance to the Logger service
     * @return \Monolog\Logger
     */
    public function getLogger() {
        return $this->services[Services::LOGGER_SERVICE];
    }

    /**
     * Retuns the current Requests context
     * @return RequestContext
     */
    public function getRequestContext() {
        return $this->services[Services::REQUEST_CONTEXT];
    }

    /**
     * Retuns a reference to the Database object
     * @return \Blend\Database\Database
     */
    public function getDatabase() {
        if (!isset($this->services[Services::DATABASE_SERVICE])) {
            $this->createDatabaseService();
        }
        return $this->getService(Services::DATABASE_SERVICE);
    }

    /**
     * Retuns a reference to the Translator object
     * @return Blend\Translation;
     */
    public function getTranslator() {
        return $this->getService(Services::TRANSLATION_SERVICE);
    }

    /**
     * Creates the PDF Printer service
     */
    private function createPDFPrinterService() {
        $this->registerService(Services::PDF_PRINTER_SERVICE, new PDFPrinterService($this));
    }

    /**
     * * Creates the Mailer service
     */
    private function createMailerService() {
        $this->registerService(Services::EMAIL_SERVICE, new MailerService($this));
    }

    /**
     * * Creates the Translation service
     */
    private function createTranslationService() {
        $this->registerService(Services::TRANSLATION_SERVICE, new Translator($this));
    }

    /**
     * Creates the URL Matcher service
     */
    private function createUrlMatcherService() {
        $urlMatcher = new UrlMatcher($this->routes, $this->getService(Services::REQUEST_CONTEXT));
        $this->registerService(Services::URL_MATCHER_SERVICE, $urlMatcher);
    }

    /**
     * Creates the UrlGenerator service
     */
    private function createUrlGeneratorService() {
        $urlGenerator = new UrlGenerator($this->routes, $this->getService(Services::REQUEST_CONTEXT, $this->getLogger()));
        $this->registerService(Services::URL_GENERATOR_SERVICE, $urlGenerator);
    }

    /**
     * Creates the database service
     */
    private function createDatabaseService() {

        $config = array(
            'database' => $this->getConfig('database.database', 'blend'),
            'username' => $this->getConfig('database.username', 'postgres'),
            'password' => $this->getConfig('database.password', 'postgres'),
            'host' => $this->getConfig('database.host', 'localhost'),
            'port' => $this->getConfig('database.port', 5432),
        );

        $this->registerService(Services::DATABASE_SERVICE, new Database($config, $this->getLogger(), $this->isDevelopment()));
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

        $requestContext = $this->getService(Services::REQUEST_CONTEXT);

        $this->getDispatcher()->addSubscriber(new LocaleServiceListener($this, $this->getConfig('translation.defaultLocale', 'en'), $requestContext));
        $this->getDispatcher()->addSubscriber(new StringToResponseListener());
        $this->getDispatcher()->addSubscriber(new JsonToResponseListener());
        $this->getDispatcher()->addSubscriber(new SessionServiceListener());
        $this->getDispatcher()->addSubscriber(new SecurityServiceListener($this));
        $this->getDispatcher()->addSubscriber(new StaticResourceListener($this->rootFolder . '/web'));
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
        $config = new Configiration("{$this->rootFolder}/config/{$this->name}-{$this->environment}-config.php");
        $this->registerService(Services::CONFIG_SERVICE, $config);
    }

    /**
     * Creates the Logger service
     */
    private function createLoggerService() {
        $logger = new Logger($this->environment);
        $level = $this->isProduction() ? Logger::WARNING : Logger::DEBUG;
        $logger->pushHandler(
                new StreamHandler("{$this->rootFolder}/var/logs/{$this->name}-{$this->environment}.log", $level)
        );
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
        $this->request = $request;
        $this->setRequestAttributes($request);
        return $this->getHttpKernel()->handle($request, $type, $catch);
    }

    /**
     * Sets the request attributes extracted from a matched route
     */
    protected function setRequestAttributes(Request $request) {
        $request->attributes->add($this->getUrlMatcher()->matchRequest($request));
    }

    /**
     * Handles the request and delivers the response.
     *
     * @param Request|null $request Request to process
     */
    public function run(Request $request = null) {

        try {

            $this->registerServices();
            $this->registerModules();

            if (null === $request) {
                $request = Request::createFromGlobals();
            }
            $response = $this->handle($request);
        } catch (ResourceNotFoundException $e) {
            $response = $this->resourceNotFoundResponse($e->getMessage());
        } catch (\Exception $e) {
            $response = $this->fatalExceptionResponse($e);
        }
        $response->send();

        $this->terminate($request, $response);
    }

    /**
     * Creates a fatal exception response (500)
     * @param type $message
     * @param type $exception
     */
    protected function fatalExceptionResponse(\Exception $exception) {

        if ($this->isDevelopment()) {
            $message = $exception->getMessage();
        } else {
            $message = "We are experiencing some technical difficulties. Please try again later.";
        }
        if (!($exception instanceof DatabaseQueryException)) {
            $this->getLogger()->error($exception->getMessage(), array(
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'stack' => $exception->getTraceAsString()
            ));
        }
        return new Response($message, 500);
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
    public function isProduction() {
        return $this->environment === Environments::PRODUCTION;
    }

    /**
     * Check if this application is in development mode
     * @return boolean
     */
    public function isDevelopment() {
        return $this->environment === Environments::DEVELOPMENT;
    }

    /**
     * Handles the application termination using the HttpKernel object
     * @param Request $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response) {
        try {
            $this->getHttpKernel()->terminate($request, $response);
        } catch (\Exception $exception) {
            $this->getLogger()->error($exception->getMessage(), array(
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'stack' => $exception->getTraceAsString()
            ));
        }
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
    protected function getService($name) {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        } else {
            return null;
        }
    }

    /**
     * Geta configuration value
     * @param type $name
     * @param type $default
     * @return type
     */
    public function getConfig($name, $default = null) {
        return $this->getService(Services::CONFIG_SERVICE)->get($name, $default);
    }

    /**
     * Initiate logout by clearing the session
     * @param type $redirectTo
     * @return RedirectResponse
     */
    public function logout($redirectTo = '/') {
        $this->request->getSession()->clear();
        return new RedirectResponse($redirectTo);
    }

    /**
     * Redirect to the current page
     * @return RedirectResponse
     */
    public function redirectSelf() {
        return new RedirectResponse($this->request->getUri());
    }

    /**
     * Redirect to a given route
     * @param type $route
     * @param type $parameters
     * @param type $referenceType
     * @return RedirectResponse
     */
    public function redirectToRoute($route, $parameters = array(), $referenceType = UrlGenerator::ABSOLUTE_PATH) {
        return new RedirectResponse($this->generateUrl($route, $parameters, $referenceType));
    }

}
