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
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $session = $event->getRequest()->getSession();
        if (!$session->has(self::SEC_SUTHENTICATED_USER)) {
            $session->set(self::SEC_SUTHENTICATED_USER, new User());
        }
        $event->getRequest()->attributes->set(self::SEC_SUTHENTICATED_USER, $session->get(self::SEC_SUTHENTICATED_USER));
    }

    public function onKernelController(FilterControllerEvent $event) {
        $this->application->setUser($event->getRequest()->getSession()->get(self::SEC_SUTHENTICATED_USER));
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', -120),
            KernelEvents::CONTROLLER => array('onKernelController', -120)
        );
    }

}
