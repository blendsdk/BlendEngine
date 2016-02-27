<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Service;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Blend\Framework\Application\ApplicationEvents;

/**
 * ControllerResolverService
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ControllerResolverService implements EventSubscriberInterface {

    public static function getSubscribedEvents() {
        return [
            ApplicationEvents::EVENT_RESOLVE_CONTROLLER => 100
        ];
    }

}
