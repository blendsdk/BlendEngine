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

use Blend\Core\Application;
use Blend\Core\Services;
use Blend\Core\Controller as ControlerBase;

/**
 * Base controller for all web applications in BlendEngine
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Controller extends ControlerBase {

    /**
     * Reference to the Twig template engine
     * @var \Twig_Environment;
     */
    protected $renderer;

    public function __construct(Application $application) {
        parent::__construct($application);
        $this->renderer = $this->application->getService(Services::TWIG_RENDERER);
    }

    /**
     * Renders a view using twig and retuns the results as string
     * @param string $viewFile
     * @param array $context
     * @return string
     */
    public function renderView($viewFile, $context) {
        $context['app'] = $this->application;
        return $this->renderer->render($viewFile, $context);
    }

}
