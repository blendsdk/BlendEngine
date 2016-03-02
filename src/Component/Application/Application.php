<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Application {

    protected abstract function handleRequest(Request $request);

    protected abstract function finalizeResponse(Response $response);

    protected abstract function handleRequestException(\Exception $ex, Request $request);

    public function run(Request $request = null) {
        try {
            if ($request === null) {
                $request = Request::createFromGlobals();
            }
            $response = $this->handleRequest($request);
            if (!($response instanceof Response)) {
                throw new \Exception(
                'The handleRequest did not return a valid Response object'
                );
            }
        } catch (\Exception $ex) {
            $response = $this->handleRequestException($ex, $request);
        }
        $this->finalizeResponse($response);
        $response->send();
    }

}
