<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Application\Stubs;

use Blend\Component\Application\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of SanityApp.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SanityApp extends Application
{
    protected function handleRequestException(\Exception $ex, Request $request)
    {
        return new Response($ex->getMessage());
    }

    protected function handleRequest(Request $request)
    {
    }

    protected function initialize()
    {
    }

    protected function finalizeResponse(Response $response)
    {
    }
}
