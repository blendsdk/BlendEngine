<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Blend\Component\HttpKernel\KernelEvents;
use Blend\Component\HttpKernel\Event\GetResponseEvent;
use Blend\Component\DI\Container;
use Blend\Component\Routing\Route;
use Blend\Component\Security\Security;
use Blend\Framework\Security\Provider\SecurityProviderInterface;

/**
 * Listens to the incomming requests and handles the security based on the
 * Route
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SecurityHandler implements EventSubscriberInterface {

    /**
     * @var Container
     */
    protected $container;

    public function onRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $this->container = $event->getContainer();

        /* @var $routes RouteCollection */
        $routes = $this->container->get(RouteCollection::class);
        /* @var $route Route */
        $route = $routes->get($request->attributes->get('_route'));
        $accessMethod = $route->getAccessMethod();

        if ($accessMethod === Security::ACCESS_PUBLIC) {
            return; //no-op
        } else {
            $handler = $this->getSecurityHandler($route->getSecurityType());
            if ($handler !== null) {
                $response = $handler->hanlde($accessMethod, $route);
                if ($response instanceof Response) {
                    $event->setResponse($response);
                }
            }
        }
    }

    /**
     * @param mixed $type
     * @return SecurityProviderInterface
     */
    private function getSecurityHandler($type) {
        $providers = $this->container->getByInterface(SecurityProviderInterface::class);
        foreach ($providers as $provider) {
            /* @var $provider SecurityProviderInterface */
            if ($provider->getHandlerType() === $type) {
                return $provider;
            }
        }
        /* @var $logger LoggerInterface */
        $logger = $this->container->get(LoggerInterface::class);
        $logger->warning("The requested security provides was" .
                " not met! Check your services", ['type' => $type]);
        return null;
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST => ['onRequest'
                , KernelEvents::PRIORITY_HIGHT + 900]
        ];
    }

}
