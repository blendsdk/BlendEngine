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

use Blend\Component\Configuration\Configuration;
use Blend\Component\DI\Container;
use Blend\Component\DI\ObjectFactoryInterface;
use Blend\Component\Templating\Twig\Extension\CommonTwigEngineExtensions;
use Blend\Component\Templating\Twig\Extension\TwigEngineExtensionProviderInterface;
use Blend\Component\Templating\Twig\TwigEngine;

/**
 * Factory class for creating a TwigEngine object.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigEngineFactory implements ObjectFactoryInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var string
     */
    protected $cacheFolder;

    public function __construct(Container $container, Configuration $config, $_app_cache_folder, $_debug = false)
    {
        $this->container = $container;
        $this->config = $config;
        $this->container->defineClass(CommonTwigEngineExtensions::class);
        $this->debug = $_debug;
        $this->cacheFolder = $_app_cache_folder;
    }

    public function create()
    {
        $twigEngine = new TwigEngine(null, $this->cacheFolder, $this->debug);

        $providers = $this->container
                ->getByInterface(TwigEngineExtensionProviderInterface::class);
        foreach ($providers as $provider) {
            /* @var $provider TwigEngineExtensionProviderInterface */
            $provider->configure($twigEngine, $this->container);
        }

        return $twigEngine;
    }
}
