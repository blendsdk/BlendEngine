<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\HttpKernel\Event;

use Symfony\Component\HttpFoundation\Response;

/**
 * Description of GetRequestEvent.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class GetResponseEvent extends KernelEvent
{
    /**
     * @var Response
     */
    protected $response;

    public function hasResponse()
    {
        return $this->response !== null;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        return $this->response = $response;
        $this->stopPropagation();
    }
}
