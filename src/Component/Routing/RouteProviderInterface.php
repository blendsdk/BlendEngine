<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Routing;

use Blend\Component\Routing\RouteBuilder;

/**
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface RouteProviderInterface {

    public function loadRoutes(RouteBuilder $builder);
}
