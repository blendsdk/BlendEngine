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
use Psr\Log\LoggerInterface;
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

    public function __construct(LoggerInterface $logger, Application $application) {
        parent::__construct($logger);
        $this->application = $application;
    }

    protected function instantiateController($class) {
        $refClass = new \ReflectionClass($class);
        $controller = new $class($this->application);
        $this->application->getTranslator()->loadTranslations(dirname($refClass->getFileName()));
        return $controller;
    }

}
