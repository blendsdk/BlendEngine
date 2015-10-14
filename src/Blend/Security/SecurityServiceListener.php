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

use Blend\Security\AnonymousUser;
use Blend\Core\Application;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Blend\Security\IUser;
use Blend\Core\Module;

/**
 * SecurityServiceListener
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SecurityServiceListener implements EventSubscriberInterface {

    const SEC_AUTHENTICATED_USER = '_sec_authenticated_user';
    const SEC_REFERER = '_sec_referer';

    private $application;
    private $referrer;

    public function __construct(Application $application) {
        $this->application = $application;
        $this->createLogoutRoute();
    }

    /**
     * Clears the session cache for this for and redirects the request to after_logout_path
     * @return RedirectResponse
     */
    public function logoutUser() {
        return $this->application->logout($this->getAfterLogoutPath());
    }

    private function getCurrentRoute($request) {
        return $this->application->getUrlMatcher()->matchRequest($request);
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $session = $request->getSession();
        $user = $this->getCurrentUser($session);
        $route = $this->application->getRoutes()->get($request->attributes->get('_route'));

        $this->application->setUser($user);

        if ($user->isAuthenticated()) {
            if ($route->getDefault('anonymous-only') || $route->getDefault('_route_name_') === Module::ROUTE_LOGIN) {
                $referer = $session->get(self::SEC_REFERER);
                if (is_null($referer) || $referer === $this->getLoginPath()) {
                    $referer = $this->getEntryPointPath();
                }
                $event->setResponse(new RedirectResponse($referer));
            }
        } else {
            if ($route->getDefault('secure')) {
                $session->set(self::SEC_REFERER, $request->getUri());
                $event->setResponse(new RedirectResponse($this->getLoginPath()));
            }
        }
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
     * Gets the current user registered in the Session
     * @param Session $session
     * @return IUser
     */
    private function getCurrentUser($session) {
        $user = null;
        if (!$session->has(self::SEC_AUTHENTICATED_USER)) {
            $user = new AnonymousUser();
            $session->set(self::SEC_AUTHENTICATED_USER, $user);
        } else {
            $user = $session->get(self::SEC_AUTHENTICATED_USER);
        }
        return $user;
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest')
        );
    }

    private function getSecurityPath($name, $default = null) {
        if ($this->application->getRoutes()->get($name)) {
            return $this->application->generateUrl($name);
        } else if (!empty($default)) {
            return $this->getSecurityPath($default);
        } else {
            throw new \LogicException("Missing the \"{$name}\" Route.");
        }
    }

    private function getLoginPath() {
        return $this->getSecurityPath(Module::ROUTE_LOGIN);
    }

    private function getAfterLogoutPath() {
        return $this->getSecurityPath(Module::ROUTE_AFTER_LOGOUT, Module::ROUTE_LOGIN);
    }

    private function getEntryPointPath() {
        return $this->getSecurityPath(Module::ROUTE_SECURED_ENTRY_POINT);
    }

}
