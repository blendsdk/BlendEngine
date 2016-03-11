<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Blend\Component\HttpKernel\KernelEvents;
use Blend\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Blend\Component\DI\Container;
use Blend\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Description of SecurityHandler
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SecurityHandler implements EventSubscriberInterface {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var RouteCollection
     */
    protected $routes;

    public function onRequest(GetResponseEvent $event) {
        $this->request = $event->getRequest();
        $this->container = $event->getContainer();
        /* @var $routes RouteCollection */
        $routes = $this->container->get(RouteCollection::class);
        /* @var $route Route */
        $route = $routes->get($this->request->attributes->get('_route'));
        $accessMethod = $route->getAccessMethod();
        if ($accessMethod === Route::ACCESS_PUBLIC) {
            return null;
        }
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST => ['onRequest'
                , KernelEvents::PRIORITY_HIGHT + 900]
        ];
    }

}
