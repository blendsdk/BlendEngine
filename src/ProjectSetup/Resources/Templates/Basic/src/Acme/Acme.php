<?php

namespace Acme;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Blend\Component\Routing\RouteProviderInterface;
use Blend\Component\Routing\RouteBuilder;
use Acme\AcmeController;

/**
 * Acme
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Acme implements RouteProviderInterface {

    public function loadRoutes(RouteBuilder $builder) {
        $builder->route('home', '/', [AcmeController::class, 'index']);
    }

}
