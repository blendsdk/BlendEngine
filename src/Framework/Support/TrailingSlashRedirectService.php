<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Support;

use Blend\Component\Routing\RouteAttribute;
use Blend\Component\Routing\RouteBuilder;
use Blend\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Redirects a request ending with a trailing slash to the same URL without
 * the trailing slash.
 * This controller is automatically added to the Application instance.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TrailingSlashRedirectService implements RouteProviderInterface
{
    /**
     * Handles the trailing slash routes by redirect to the same URL without the
     * trailing slash.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function trailingSlashHandler(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();
        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return new RedirectResponse($url, 301);
    }

    public function loadRoutes(RouteBuilder $builder)
    {
        $defaults = array(
            RouteAttribute::CONTROLLER => array(self::class, 'trailingSlashHandler'),
        );
        $requirements = array(
            'url' => '.*/$',
        );
        $options = array(
            'method' => 'GET',
        );
        $builder->addRoute('trailingSlashRoute', '/{url}', $defaults, $requirements, $options);
    }
}
