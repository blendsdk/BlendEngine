<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Application\Stubs;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Blend\Component\HttpKernel\KernelEvents;
use Blend\Component\HttpKernel\Event\GetExceptionResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Description of CustomRequestExceptionHandler
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CustomRequestExceptionHandler implements EventSubscriberInterface {

    public function onRequestException(GetExceptionResponseEvent $event) {
        $ex = $event->getEception();
        if ($ex instanceof ResourceNotFoundException) {
            $response = new Response('Page not found ' . $event->getRequest()->getPathInfo(), 404);
        } else {
            $response = new Response('Server error', 500);
        }
        $event->setResponse($response);
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST_EXCEPTION => 'onRequestException'
        ];
    }

//put your code here
}
