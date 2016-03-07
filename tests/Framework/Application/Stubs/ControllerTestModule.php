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
use Blend\Component\Routing\RouteBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

    public function loadRoutes(RouteBuilder $builder) {
        $builder->route('no-response', '/no-response', []);
        $builder->route('ping', '/ping', [self::class, 'ping']);
        $builder->route('hello', '/hello/{fname}/{lname}', [self::class, 'hello']);
        $builder->route('api', '/api/{key}/{value}', [self::class, 'api'])
                ->setAPIRoute();
    }

}
