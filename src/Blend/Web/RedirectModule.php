<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Web;

use Blend\Core\Module;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * RedirectModule redirects a request trailing slash to the same URL without the
 * trailing slash
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class RedirectModule extends Module {

    /**
     * Registeres a Route for handling the trailing slash
     */
    private function createTrailinglashRoute() {
        $defaults = array(
            '_controller' => array($this, 'trailinglashHandler')
        );
        $requirements = array(
            'url' => '.*/$'
        );
        $options = array(
            'method' => 'GET'
        );
        $this->application->addRoute('trailing_slash', new Route('/{url}', $defaults, $requirements, $options));
    }

    /**
     * Handles the trailing slash routes by redirect to the same URL without the
     * trailing slash
     * @param Request $request
     * @return RedirectResponse\
     */
    public function trailinglashHandler(Request $request) {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();
        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);
        return new RedirectResponse($url, 301);
    }

    protected function createRoutes() {
        $this->createTrailinglashRoute();
    }

}
