<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Security;

use Blend\Security\User;
use Blend\Core\Application;
use Blend\Security\SecurityUrlMatcher;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
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
        $this->createLogoutRoute();
    }

    /**
     * Creates a login route (config: logout_path)
     */
    private function createLogoutRoute() {
        if (is_null($this->application->getRoutes()->get('logout'))) {
            $this->application->addRoute('logout', new Route('/logout', array(
                '_controller' => array($this, 'logoutUser')
            )));
        }
    }

    /**
     * Clears the session cache for this for and redirects the request to after_logout_path
     * @return RedirectResponse
     */
    public function logoutUser() {
        return $this->application->logout($this->getAfterLogoutPath());
    }

    /**
     * Chechs the current user's authentication and redirects to login_path
     * if needed
     */
    public function onKernelRequest(GetResponseEvent $event) {
        $session = $event->getRequest()->getSession();

        if (!$session->has(self::SEC_SUTHENTICATED_USER)) {
            $session->set(self::SEC_SUTHENTICATED_USER, new User());
        }
        $user = $session->get(self::SEC_SUTHENTICATED_USER);
        if (!$user->isAuthenticated() && $this->needsAuthentication($event->getRequest())) {
            $event->setResponse(new RedirectResponse($this->getLoginPath()));
        } else {
            $this->application->setUser($user);
        }
    }

    /**
     * Checks if the current request is needed authentication
     * @param Request $request
     * @return type
     */
    private function needsAuthentication(Request $request) {
        $routes = new RouteCollection();
        foreach ($this->application->getRoutes()->all() as $name => $route) {
            if ($route->getDefault('secure')) {
                $routes->add($name, $route);
            }
        }
        $urlMatcher = new SecurityUrlMatcher($routes, $this->application->getRequestContext());
        $urlMatcher->getContext()->fromRequest($request);
        return $urlMatcher->match($request->getPathInfo());
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest')
        );
    }

    private function getAfterLogoutPath() {
        if ($this->application->getRoutes()->get('after_logout')) {
            return $this->application->generateUrl('after_logout');
        } else {
            return '/';
        }
    }

    private function getLoginPath() {
        if ($this->application->getRoutes()->get('login')) {
            return $this->application->generateUrl('login');
        } else {
            return '/login';
        }
    }

}
