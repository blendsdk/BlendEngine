<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Web;

use Blend\Core\Services;
use Blend\Web\RedirectModule;
use Blend\Core\Application as BaseApplication;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;

/**
 * Base class for all web application. This class provides the twig renderer as
 * template rendering engine and is availble from \Blend\Web\Controller
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Application extends BaseApplication {

    protected function registerModules() {
        parent::registerModules();
        $this->modules[] = new RedirectModule($this);
    }

    protected function registerServices() {
        parent::registerServices();
        $this->registerTwigRenderer();
    }

    /**
     * Registers the twig renderer
     */
    protected function registerTwigRenderer() {
        $loader = new \Twig_Loader_Filesystem($this->rootFolder);
        $twig = new \Twig_Environment($loader, array(
            'cache' => $this->isProduction() ? "{$this->rootFolder}/var/cache" : false,
            'debug' => $this->isDevelopment(),
            'strict_variables' => true
        ));
        $twig->addExtension(new TranslationExtension($this->getTranslator()));
        $twig->addExtension(new RoutingExtension($this->getUrlGenerator()));
        $this->registerService(Services::TWIG_RENDERER, $twig);
    }

    public function getRenderer() {
        return $this->getService(Services::TWIG_RENDERER);
    }

}
