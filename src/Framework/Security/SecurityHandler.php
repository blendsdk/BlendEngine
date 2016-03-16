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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Blend\Component\HttpKernel\KernelEvents;
use Blend\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Blend\Component\DI\Container;
use Blend\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Blend\Framework\Security\User\UserProviderInterface;
use Blend\Framework\Security\User\Guest;
use Blend\Framework\Security\SecurityProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Listens to the incomming requests and handles the security based on the
 * Route
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SecurityHandler implements EventSubscriberInterface {

    const REFERER_URL = '_http_referer';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var UserProviderInterface
     */
    protected $currentUser;

    public function onRequest(GetResponseEvent $event) {
        $this->request = $event->getRequest();
        $this->container = $event->getContainer();

        /* @var $routes RouteCollection */
        $routes = $this->container->get(RouteCollection::class);
        /* @var $route Route */
        $route = $routes->get($this->request->attributes->get('_route'));
        $accessMethod = $route->getAccessMethod();


        if ($accessMethod === Route::ACCESS_PUBLIC) {
            return; //no-op
        } else if ($accessMethod === Route::ACCESS_GUEST_ONLY) {
            $this->handleAccessGuestOnly($event);
        } else if ($accessMethod === Route::ACCESS_AUTHORIZED_USER) {
            $this->handleAccessAuthorizedUser($event, $route);
        }
    }

    private function handleAccessGuestOnly(GetResponseEvent $event) {
        $user = $this->getCurrentUser();
        if (!$user->isGuest()) {
            $referer = $this->getReferer();
            if ($referer !== null) {
                $event->setResponse(new RedirectResponse($referer));
            } else {
                $event->setResponse($handler->delegateToEntryPoint());
            }
        }
    }

    private function handleAccessAuthorizedUser(GetResponseEvent $event
    , Route $route) {

        $handler = $this->getSecurityHandler($route->getSecurityType());
        if ($handler !== null) {
            $user = $this->getCurrentUser();
            if ($user->isGuest()) {
                $this->saveReferer();
                $event->setResponse($handler->startAuthentication());
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

    /**
     * Tries to get the current user from the container
     * @return null|User\UserProviderInterface
     */
    private function getCurrentUser() {
        return $this->request->getSession()->get('_authenticated_user', new Guest());
    }

    private function saveReferer() {
        $referer = $this->request->getUri();
        $this->request->getSession()->set(self::REFERER_URL
                , $this->request->getUri());
    }

    private function getReferer() {
        $session = $this->request->getSession();
        $result = $session->get(self::REFERER_URL, null);
        if ($result !== null) {
            $session->remove(self::REFERER_URL);
        }
        return $result;
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST => ['onRequest'
                , KernelEvents::PRIORITY_HIGHT + 900]
        ];
    }

}
