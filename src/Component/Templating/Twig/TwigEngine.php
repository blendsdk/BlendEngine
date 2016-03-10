<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Templating\Twig;

use Blend\Component\Templating\EngineInterface;

/**
 * TwigEngine provides rendering Twig templates
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigEngine implements EngineInterface {

    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    public function __construct($viewRootFolder, $cacheFolder, $debug = false) {
        $this->twigEnvironment = $this->createTwig($viewRootFolder
                , $cacheFolder
                , $debug);
    }

    /**
     * Creates a Twig rendering engine
     * @param type $viewRootFolder
     * @param type $cacheFolder
     * @param type $debug
     * @return \Twig_Environment
     */
    protected function createTwig($viewRootFolder, $cacheFolder, $debug) {
        $loader = new \Twig_Loader_Filesystem($viewRootFolder);
        $twig = new \Twig_Environment($loader);
        $twig->enableStrictVariables();
        if ($debug === true) {
            $twig->enableDebug();
        } else {
            $twig->disableDebug();
            $twig->setCache($cacheFolder);
        }
        return $twig;
    }

    public function render($view, array $parameters = array()) {
        return $this->twigEnvironment->render($view, $parameters);
    }

}
