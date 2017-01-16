<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Factory;
use Blend\Component\Templating\Twig\Extension\CommonTwigEngineExtensions;
use Blend\Component\Templating\Twig\Extension\TwigEngineExtensionProviderInterface;
use Blend\Framework\Templating\Twig\TwigEngineService;

//TODO: reformat


/**
 * Factory class for creating a TwigEngine object
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigEngineFactory implements ObjectFactoryInterface {

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Configuration
     */
    protected $config;

    public function __construct(Container $container, Configuration $config) {
        $this->container = $container;
        $this->config = $config;
        $this->container->define(CommonTwigEngineExtensions::class);
    }

    public function create() {

        $twigEngine = $this->container->get(TwigEngineService::class);
        
        $providers = $this->container
                ->getByInterface(TwigEngineExtensionProviderInterface::class);
        foreach ($providers as $provider) {
            /* @var $provider TwigEngineExtensionProviderInterface */
            $provider->configure($twigEngine, $this->container);
        }

        return $twigEngine;
    }

}