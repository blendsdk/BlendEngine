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

/**
 * Provides types names of the built-in core services
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Services {

    const LOGGER_SERVICE = 'logger.service';
    const CONFIG_SERVICE = 'config.service';
    const EVENT_DISPATCHER_SERVICE = 'event.dispatcher.service';
    const HTTP_KERNEL_SERVICE = 'http.kernel.service';
    const REQUEST_CONTEXT = 'request.context';
    const TWIG_RENDERER = 'twig.renderer';
    const DATABASE_SERVICE = 'postgresql.service';

}
