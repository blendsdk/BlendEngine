<?php

namespace Blend\Framework\Support;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Blend\Component\Routing\Route;
use Blend\Component\Routing\RouteAttribute;
use Blend\Component\Routing\RouteProviderInterface;
use Blend\Component\Routing\RouteBuilder;

/**
 * Redirects a request ending with a trailing slash to the same URL without
 * the trailing slash.
 * This controller is automatically added to the Application instance
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TrailingSlashRedirectService implements RouteProviderInterface
{

    /**
     * Handles the trailing slash routes by redirect to the same URL without the
     * trailing slash
     * @param Request $request
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
            RouteAttribute::CONTROLLER => array(TrailingSlashRedirectService::class, 'trailingSlashHandler')
        );
        $requirements = array(
            'url' => '.*/$'
        );
        $options = array(
            'method' => 'GET'
        );
        $builder->add('trailingSlashRoute', new Route('/{url}', $defaults, $requirements, $options));
    }
}
