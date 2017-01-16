<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Application;

use Blend\Component\Templating\TemplateEngineInterface;
use Blend\Framework\Support\Runtime\RuntimeProviderInterface;

/**
 * Controller
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Controller {

    /**
     * @var TemplateEngineInterface
     */
    protected $renderer;

    /**
     * @var RuntimeProviderInterface
     */
    protected $runtime;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Returns an array or a folder where the views are located
     */
    protected abstract function getTemplatesFolder();

    public function __construct(
    TemplateEngineInterface $renderer
    , RuntimeProviderInterface $runtime) {
        $this->renderer = $renderer;
        $this->runtime = $runtime;
        $viewsPath = $this->getTemplatesFolder();
        if (!is_array($viewsPath)) {
            $viewsPath = array($viewsPath);
        }
        $this->renderer->setViewPaths($viewsPath);
    }

    /**
     * Wraps the renderer->render(...) function by adding:
     * runtime, request, and is_authenticated values to the view context
     * @param type $view
     * @param array $parameters
     * @return type
     */
    public function render($view, array $parameters = array()) {
        if (is_null($parameters)) {
            $parameters = array();
        }
        $parameters = array_merge(array(
            'request' => $this->runtime->getRequest(),
            'runtime' => $this->runtime,
            'is_authenticated' => !($this->runtime->getCurrentUser()->isGuest() === true)
                ), $parameters);
        return $this->renderer->render($view, $parameters);
    }

}
