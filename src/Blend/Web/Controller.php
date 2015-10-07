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

use Blend\Web\Application;
use Blend\Core\Module;
use Blend\Core\Controller as ControlerBase;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @var Request;
     */
    protected $request;

    public function __construct(Application $application, Module $module, Request $request) {
        parent::__construct($application, $module, $request);
        $this->renderer = $this->application->getRenderer();
    }

    /**
     * Renders a view using twig and retuns the results as string. The context
     * automatically has the following variables set:
     * - app: as Application container
     * - currentUser: The current user object
     * - request: The current Request object
     * - userAuthenticated: Boolean value indicating if the user is authenticated
     * @param string $viewFile
     * @param array $context
     * @return string
     */
    public function renderView($viewFile, $context = array()) {
        if (!is_array($context)) {
            $context = array();
        }
        $context['app'] = $this->application;
        $context['currentUser'] = $this->application->getUser();
        $context['userAuthenticated'] = $this->application->getUser()->isAuthenticated();
        $this->request->attributes->set('_route_params', $this->createRouteParams());
        $context['request'] = $this->request;
        return $this->renderer->render($this->checkSetViewsPath($viewFile), $context);
    }

    /**
     * Prepares values for the _route_params
     * @return type
     */
    private function createRouteParams() {
        $params = $this->request->attributes->all();
        unset($params['_controller']);
        unset($params['_view']);
        unset($params['_route']);
        return $params;
    }

    /**
     * Sets a View path corresponding to the current module
     * @param type $viewFile
     * @return type
     */
    private function checkSetViewsPath($viewFile) {
        $modulePath = str_replace($this->application->getRootFolder('/'), '', $this->module->getPath('/Views/'));
        $find = array('@module/');
        $replace = array($modulePath);
        return str_replace($find, $replace, $viewFile);
    }

}
