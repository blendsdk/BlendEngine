<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Application\Stubs;
use Blend\Framework\Application\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Description of TestableApplication
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TestableApplication extends Application {
    
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
            throw $this->handleRequestException($ex, $request);
        }
        $this->finalize($request, $response);
        return $response;
    }
    
    protected function handleRequestException(\Exception $ex, \Symfony\Component\HttpFoundation\Request $request) {
        return $ex;
    }    
    
}
