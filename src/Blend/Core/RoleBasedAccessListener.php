<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Core;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Blend\Web\Application;
use Blend\Core\Module;

/**
 * RoleBasedAccessListener check the access role of a request. If the current
 * user does not have the correct access role, then the system will logout the
 * user.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class RoleBasedAccessListener implements EventSubscriberInterface {

    /**
     * @var Application
     */
    protected $application;

    public function __construct(Application $application) {
        $this->application = $application;
    }

    public function onRequest(GetResponseEvent $event) {
        $role = $event->getRequest()->attributes->get('role', null);
        if(!empty($role)) {
            if(!$this->application->getUser()->hasRole($role)) {
                $this->application->logout();
                $event->setResponse($this->application->redirectToRoute(Module::ROUTE_SECURED_ENTRY_POINT));
            }
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onRequest'),
        );
    }

}
