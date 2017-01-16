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

    const TRIM_RESULT = '_twig_trim';

    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * @var Twig_Loader_Filesystem
     */
    protected $loader;

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
        $this->loader = new \Twig_Loader_Filesystem($viewRootFolder);
        $twig = new \Twig_Environment($this->loader);
        $twig->enableStrictVariables();
        if ($debug === true) {
            $twig->enableDebug();
        } else {
            $twig->disableDebug();
            $twig->setCache($cacheFolder);
        }
        return $twig;
    }

    /**
     * Adds an extension to the twig
     * @param \Twig_ExtensionInterface $extension
     * @return $this
     */
    public function addExtension(\Twig_ExtensionInterface $extension) {
        $this->twigEnvironment->addExtension($extension);
        return $this;
    }

    public function render($view, array $parameters = array()) {
        if (isset($parameters[self::TRIM_RESULT]) && $parameters[self::TRIM_RESULT] === true) {
            return trim($this->twigEnvironment->render($view, $parameters));
        } else {
            return $this->twigEnvironment->render($view, $parameters);
        }
    }

    /**
     * Sets the views path for this TwigEngine
     * @param array $paths
     * @return $this
     */
    public function setViewPaths(array $paths = array()) {
        $this->loader->setPaths($paths);
        return $this;
    }

}
