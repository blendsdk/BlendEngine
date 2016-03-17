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

use Blend\Component\DI\ObjectFactoryInterface;
use Blend\Framework\Translation\TranslatorService;
use Blend\Component\Translation\TranslationProviderInterface;
use Blend\Component\Configuration\Configuration;
use Blend\Component\DI\Container;
use Blend\Component\Filesystem\Filesystem;
use Blend\Component\Routing\RouteAttribute;

/**
 * Description of TranslatorFactory
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TranslatorFactory implements ObjectFactoryInterface {

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
    }

    public function create() {
        $translator = new TranslatorService(
                $this->getCurrentLocale()
                , $this->getCacheFolder()
                , $this->config->get('debug', false)
        );
        $providers = $this->container
                ->getByInterface(TranslationProviderInterface::class);
        foreach ($providers as $provider) {
            /* @var $provider TranslationProviderInterface */
            $provider->configure($translator, $this->container);
        }
        return $translator;
    }

    private function getCacheFolder() {
        /* @var $fs Filesystem */
        $fs = $this->container->get(Filesystem::class);
        return $fs->assertFolderWritable($this->container->get('_app_cache_folder'));
    }

    private function getCurrentLocale() {
        if ($this->container->isDefined(RouteAttribute::LOCALE)) {
            return $this->container->get(RouteAttribute::LOCALE);
        } else {
            return $this->config->get('translation.defaultLocale', null);
        }
    }

}
