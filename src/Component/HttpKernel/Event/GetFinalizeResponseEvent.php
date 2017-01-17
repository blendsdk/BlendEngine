<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\HttpKernel\Event;

use Blend\Component\DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of GetFinalizeResponseEvent.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class GetFinalizeResponseEvent extends GetResponseEvent
{
    public function __construct(Response $response, Request $request, Container $container)
    {
        parent::__construct($request, $container);
        $this->response = $response;
    }
}
