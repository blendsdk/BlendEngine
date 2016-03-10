<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Templating\Twig;

use Blend\Component\Templating\Twig\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Blend\Component\Templating\Twig\Extension\EuroCurrencyExtension;
use Blend\Component\Templating\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * TwigEngineService is a customized version of the
 * TwigEngine containing configuration provided
 * by the running application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigEngineService extends TwigEngine {

    /**
     * @var Request
     */
    protected $request;

    public function __construct(
    Request $request
    , $_app_cache_folder
    , $_debug
    , $viewRootFolder = null
    , UrlGeneratorInterface $urlGenerator = null) {
        /**
         * The $_debug, $_app_cache_folder parametes will be read from
         * the Service Container
         */
        parent::__construct($viewRootFolder, $_app_cache_folder, $_debug);

        $this->request = $request;

        $this->twigEnvironment->addExtension(new EuroCurrencyExtension());
        if (!is_null($urlGenerator)) {
            $this->twigEnvironment->addExtension(
                    new RoutingExtension($urlGenerator));
        }
    }

    public function render($view, array $parameters = array()) {
        if (isset($parameters['_trim'])) {
            return trim(parent::render($view, $this->normalizeParameters($parameters)));
        } else {
            return parent::render($view, $this->normalizeParameters($parameters));
        }
    }

    private function normalizeParameters(array $parameters = []) {
        $defaults = [
            'request' => $this->request
        ];
        return array_merge($defaults, $parameters);
    }

}
