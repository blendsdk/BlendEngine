<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\HttpKernel;

/**
 * KernelEvents.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class KernelEvents
{
    const REQUEST = 'http.request';
    const REQUEST_EXCEPTION = 'http.request.exception';
    const FINALIZE_RESPONSE = 'http.finalize.response';
    const PRIORITY_BOOT_SERVICE = 1000;
    const PRIORITY_SECURITY_SERVICE = 900;
    const PRIORITY_CONTROLLER_SERVICE = 800;
    const PRIORITY_HIGHT = 500;
    const PRIORITY_MEDIUM = 400;
    const PRIORITY_LOW = 300;
}
