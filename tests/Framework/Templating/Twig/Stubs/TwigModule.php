<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Templating\Twig\Stubs;

use Blend\Component\Routing\RouteBuilder;
use Blend\Component\Routing\RouteProviderInterface;

/**
 * Description of TwigModule.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigModule implements RouteProviderInterface
{
    public function loadRoutes(RouteBuilder $builder)
    {
        $builder->route('url_test', '/urltest/{_locale}', array(TwigController::class, 'urlTest'));
    }
}
