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

use Blend\Core\MimeTypes;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves static file
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class StaticResourceListener implements EventSubscriberInterface {

    protected $serveFolder;

    public function __construct($serveFolder) {
        $this->serveFolder = $serveFolder;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $filename = "{$this->serveFolder}/{$request->getRequestUri()}";
        if (stripos($filename, '?') !== false) {
            $filename = substr($filename, 0, strpos($filename, "?"));
        }
        if (file_exists($filename) && is_file($filename)) {
            $response = new Response(file_get_contents($filename));
            $response->headers->set('Content-Type', MimeTypes::getFileMIMEType($filename));
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest')
        );
    }

}
