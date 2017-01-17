<?php

namespace Acme;

use Blend\Component\Routing\RouteBuilder;
use Blend\Component\Routing\RouteProviderInterface;
use Symfony\Component\Routing\Route;

/**
 * Acme.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Acme implements RouteProviderInterface
{
    public function loadRoutes(RouteBuilder $builder)
    {
        $builder->route('home', '/', array(AcmeController::class, 'index'));
    }
}
