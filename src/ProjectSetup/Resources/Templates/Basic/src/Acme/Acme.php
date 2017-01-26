<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
