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
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Creates a JsonResponse if the value that is returned from a
 * controller::action is either an array of \ArrayObject
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class JsonToResponseListener implements EventSubscriberInterface {

    public function onKernelView(GetResponseForControllerResultEvent $event) {
        $response = $event->getControllerResult();

        if (is_array($response) || ($response instanceof \ArrayObject)) {
            $event->setResponse(new JsonResponse($response));
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::VIEW => array('onKernelView', -10),
        );
    }

}
