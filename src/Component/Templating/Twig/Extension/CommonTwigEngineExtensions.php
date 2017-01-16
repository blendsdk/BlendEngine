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
use Blend\Component\Templating\Twig\Extension\RoutingExtension;
use Blend\Component\Templating\Twig\Extension\EuroCurrencyExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\TranslatorInterface;
use Blend\Component\Templating\Twig\Extension\TwigEngineExtensionProviderInterface;


class CommonTwigEngineExtensions implements TwigEngineExtensionProviderInterface {

    public function configure(TwigEngine $twigEngine, Container $container) {

        // Loading a URL generator if it exists
        if($container->isDefined(UrlGeneratorInterface::class)) {
            $twigEngine->addExtension($container->get(RoutingExtension::class));
        }

        // Loading the translator if it exists
        if($container->isDefined(TranslatorInterface::class)) {
            $twigEngine->addExtension($container->get(TranslationExtension::class));
        }

        // loading the EURO currency symbol
        $twigEngine->addExtension($container->get(EuroCurrencyExtension::class));

    }

}