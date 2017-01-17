<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Templating\Twig\Extension;

use Blend\Component\DI\Container;
use Blend\Component\Templating\Twig\TwigEngine;

/**
 * This in interface is used to configure and load extensions into the Twig
 * rendering engine.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface TwigEngineExtensionProviderInterface
{
    public function configure(TwigEngine $twigEngine, Container $container);
}
