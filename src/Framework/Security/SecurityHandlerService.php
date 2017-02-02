<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Security;

use Blend\Component\DI\Container;
use Blend\Component\HttpKernel\Event\GetFinalizeResponseEvent;
use Blend\Component\HttpKernel\Event\GetResponseEvent;
use Blend\Component\HttpKernel\Event\KernelEvent;
use Blend\Component\HttpKernel\KernelEvents;
use Blend\Component\Routing\Route;
use Blend\Component\Security\Security;
use Blend\Framework\Security\Provider\SecurityProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the incoming requests and handles the security based on the
 * Route.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SecurityHandlerService implements EventSubscriberInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var SecurityProviderInterface
     */
    protected $securityHandler;

    /**
     * @var mixed
     */
    protected $accessMethod;

    /**
     * @var Route
     */
    protected $route;
    private $request;

    protected function initialize(KernelEvent $event)
    {
        $request = $this->request = $event->getRequest();
        $this->container = $event->getContainer();
        /* @var $routes RouteCollection */
        $routes = $this->container->get(RouteCollection::class);
        $this->route = $routes->get($request->attributes->get('_route'));
        $this->accessMethod = $this->route->getAccessMethod();
    }

    public function onRequest(GetResponseEvent $event)
    {
        $this->initialize($event);
        if ($this->accessMethod === Security::ACCESS_PUBLIC) {
            return; //no-op
        } else {
            $this->securityHandler = $this->getSecurityHandler($this->route->getSecurityType());
            if ($this->securityHandler !== null) {
                $response = $this->securityHandler->handle($this->accessMethod, $this->route);
                if ($response instanceof Response) {
                    $event->setResponse($response);
                }
            }
        }
    }

    /**
     * @param mixed $type
     *
     * @return SecurityProviderInterface
     */
    private function getSecurityHandler($type)
    {
        $providers = $this->container->getByInterface(SecurityProviderInterface::class);
        foreach ($providers as $provider) {
            /* @var $provider SecurityProviderInterface */
            if ($provider->getHandlerType() === $type) {
                return $provider;
            }
        }
        /* @var $logger LoggerInterface */
        $logger = $this->container->get(LoggerInterface::class);
        $logger->warning('The requested security provides was' .
                ' not met! Check your services', array('type' => $type, 'request' => $this->request->getRequestUri()));

        return null;
    }

    public function onResponse(GetFinalizeResponseEvent $event)
    {
        if ($this->securityHandler !== null) {
            $this->securityHandler->finalize($this->accessMethod, $this->route, $event->getResponse());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onRequest', KernelEvents::PRIORITY_HIGHT + 900),
            KernelEvents::FINALIZE_RESPONSE => array('onResponse', KernelEvents::PRIORITY_HIGHT + 900),
        );
    }
}
