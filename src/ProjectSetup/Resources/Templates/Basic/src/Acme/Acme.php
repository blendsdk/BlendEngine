<?php

namespace Acme;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Blend\Component\Routing\RouteProviderInterface;
use Acme\AcmeController;

/**
 * Acme
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Acme implements RouteProviderInterface {

    public function loadRoutes(RouteCollection $collection) {
        $collection->add('home', new Route('/', [
            '_controller' => [AcmeController::class, 'index']
        ]));
    }

}
