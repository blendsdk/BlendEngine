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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;

/**
 * Finds a controller for a request
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ControllerListener implements EventSubscriberInterface {

    /**
     * @var RouteCollection
     */
    private $routes;

    /**
     * @var RequestContext
     */
    private $requestContext;

    public function __construct(RouteCollection $routs, RequestContext $requestContext) {
        $this->routes = $routs;
        $this->requestContext = $requestContext;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $urlMatcher = new UrlMatcher($this->routes, $this->requestContext);
        $urlMatcher->getContext()->fromRequest($request);
        $request->attributes->add($urlMatcher->match($request->getPathInfo()));
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest')
        );
    }

}
