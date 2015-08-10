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

use Blend\Core\Application;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Description of LocaleServiceListener
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class LocaleServiceListener implements EventSubscriberInterface {

    private $application;
    private $defaultLocale;
    private $requestContext;

    public function __construct(Application $application, $defaultLocale = 'en', RequestContext $requestContext) {
        $this->application = $application;
        $this->defaultLocale = $defaultLocale;
        $this->requestContext = $requestContext;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $request->setDefaultLocale($this->defaultLocale);

        $this->setLocale($request);
        $this->setRouterContext($request);

        $this->application->setLocale($request->getLocale());
    }

    private function setLocale(Request $request) {
        if ($locale = $request->attributes->get('_locale')) {
            $request->setLocale($locale);
        }
    }

    private function setRouterContext(Request $request) {
        if (null !== $this->requestContext) {
            $this->requestContext->setParameter('_locale', $request->getLocale());
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 0)
        );
    }

}
