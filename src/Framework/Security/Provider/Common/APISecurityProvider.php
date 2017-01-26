<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Security\Provider\Common;

use Blend\Component\Routing\Route;
use Blend\Component\Security\Security;
use Blend\Framework\Security\Provider\SecurityProvider;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class APISecurityProvider extends SecurityProvider
{
    abstract protected function receiveBrowserCookies();

    public function finalize($accessMethod, Route $route, Response $response)
    {
        $response->headers->set('Access-Control-Allow-Origin', 'http://127.0.0.1:8001');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', 'token');
    }

    public function getHandlerType()
    {
        return Security::SECURITY_TYPE_API;
    }

    public function handle($accessMethod, Route $route)
    {
    }
}
