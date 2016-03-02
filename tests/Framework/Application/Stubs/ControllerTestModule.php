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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Blend\Component\Routing\RouteProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Description of ControllerTestModule
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ControllerTestModule implements RouteProviderInterface {

    public function ping() {
        return new Response('pong');
    }

    public function hello($fname, $lname, Request $request) {
        return new Response("Hello {$fname} {$lname} from {$request->getPathInfo()}");
    }

    public function api($key, $value) {
        return [$key => $value];
    }

    public function loadRoutes(RouteCollection $collection) {
        $collection->add('no-response', new Route('/no-response'));
        $collection->add('ping', new Route('/ping', [
            '_controller' => [self::class, 'ping']
        ]));
        $collection->add('hello', new Route('/hello/{fname}/{lname}', [
            '_controller' => [self::class, 'hello']
        ]));
        $collection->add('api', new Route('/api/{key}/{value}', [
            '_controller' => [self::class, 'api']
        ]));
    }

}
