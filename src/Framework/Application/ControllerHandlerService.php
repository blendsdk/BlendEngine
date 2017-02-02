<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Application;

use Blend\Component\DI\Container;
use Blend\Component\HttpKernel\Event\GetResponseEvent;
use Blend\Component\HttpKernel\KernelEvents;
use Blend\Component\Routing\RouteAttribute;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * This service is used to load and call the correct controllers
 * based on Route definition.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ControllerHandlerService implements EventSubscriberInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(Container $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $this->assertControllerKey($request);
        $controller = $request->attributes->get(RouteAttribute::CONTROLLER);
        if ($this->isArrayDefinition($controller)) {
            $response = $this->container->call($controller[0], $controller[1], $this->mergeRequestAttributes($request));
            $event->setResponse($this->createResponse($request, $response));
        } else {
            $error = 'The controller has an invalid [controller,action] signature!' .
                    ' You should check the Route creation!';
            $this->logger->error($error, array('RequestAttributes' => $request->attributes->add(), $request->getPathInfo()));
            throw new InvalidParameterException($error);
        }
    }

    /**
     * Creates a Response based on the controller/action result.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response|JsonResponse
     */
    private function createResponse(Request $request, $response)
    {
        if ($response instanceof Response) {
            return $response;
        } else {
            if ($request->attributes->get(RouteAttribute::JSON_RESPONSE, false)) {
                return new JsonResponse($response);
            } else {
                return new Response($response);
            }
        }
    }

    /**
     * Merges the request attributes into one array.
     *
     * @param Request $request
     *
     * @return array
     */
    private function mergeRequestAttributes(Request $request)
    {
        return array_merge(
                $request->attributes->all(), $request->request->all(), $request->query->all()
        );
    }

    /**
     * Assert if the matched route contains a controller key/pare.
     *
     * @param Request $request
     *
     * @throws InvalidParameterException
     */
    protected function assertControllerKey(Request $request)
    {
        if (!$request->attributes->has(RouteAttribute::CONTROLLER)) {
            $error = 'The matched route does not have a controller ' .
                    'key/value pair. You should check the Route creation!';
            $this->logger->error($error, array('RequestAttributes' => $request->attributes->add(), $request->getPathInfo()));
            throw new InvalidParameterException($error);
        }
    }

    /**
     * Check the signature of the provided controller.
     *
     * @param array $controller
     *
     * @return bool
     */
    protected function isArrayDefinition($controller)
    {
        if (is_array($controller) && count($controller) == 2 && is_string($controller[0]) && is_string($controller[1])
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onRequest', KernelEvents::PRIORITY_CONTROLLER_SERVICE),
        );
    }
}
