<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Core;

use Blend\Core\Application;
use Blend\Core\Module;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as ControllerResolverBase;

/**
 * Specialized ControllerResolver for BlendEngine. Controllers in BlendEngine
 * must base a constructor with Application as parameter. This resolvers
 * makes sure the controller in instantiated with the Application parameter
 */
class ControllerResolver extends ControllerResolverBase {

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Module
     */
    protected $module;

    public function __construct(LoggerInterface $logger, Application $application) {
        parent::__construct($logger);
        $this->application = $application;
    }

    public function getController(Request $request) {
        $this->module = $request->attributes->get('_module_');
        $request->attributes->remove('_module_');
        return parent::getController($request);
    }

    protected function instantiateController($class) {
        $refClass = new \ReflectionClass(get_class($this->module));
        $controller = new $class($this->application, $this->module);
        $this->application->getTranslator()->loadTranslations(dirname($refClass->getFileName()));
        return $controller;
    }

}
