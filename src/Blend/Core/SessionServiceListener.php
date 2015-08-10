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

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * SessionServiceListener
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SessionServiceListener implements EventSubscriberInterface {

    protected $sessionObject;

    public function __construct() {
        $this->sessionObject = new Session(
                new NativeSessionStorage(array(), new NativeFileSessionHandler()
                )
        );
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $event->getRequest()->setSession($this->sessionObject);
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', -10),
        );
    }

}
