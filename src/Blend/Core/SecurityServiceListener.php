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

use Blend\Security\User;
use Blend\Core\Application;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Blend\Core\Services;
use Blend\Security\SecurityUrlMatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Route;

/**
 * SecurityServiceListener
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SecurityServiceListener implements EventSubscriberInterface {

    const SEC_SUTHENTICATED_USER = '_authenticated_user';

    private $application;

    public function __construct(Application $application) {
        $this->application = $application;
        $this->logoutPathName = $application->getConfig('logout_path', '/logout');
        $application->getRoutes()->add('logout', new Route($this->application->getConfig('logout_path', '/logout'), array(
            '_controller' => array($this, 'logoutUser')
        )));
    }

    public function logoutUser(Request $request) {
        $request->getSession()->clear();
        return new RedirectResponse($this->application->getConfig('after_logout_path', '/'));
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $session = $event->getRequest()->getSession();

        if (!$session->has(self::SEC_SUTHENTICATED_USER)) {
            $session->set(self::SEC_SUTHENTICATED_USER, new User());
        }
        $user = $session->get(self::SEC_SUTHENTICATED_USER);
        $this->application->setUser($user);
        if (!$user->isAuthenticated() && $this->needAuthentication($event->getRequest())) {
            $event->setResponse(new RedirectResponse($this->application->getConfig('login_path', '/login')));
        }
    }

    private function needAuthentication(Request $request) {
        $routes = new RouteCollection();
        foreach ($this->application->getRoutes()->all() as $name => $route) {
            if ($route->getDefault('secure')) {
                $routes->add($name, $route);
            }
        }
        $urlMatcher = new SecurityUrlMatcher($routes, $this->application->getService(Services::REQUEST_CONTEXT));
        $urlMatcher->getContext()->fromRequest($request);
        return $urlMatcher->match($request->getPathInfo());
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', -120)
        );
    }

}
