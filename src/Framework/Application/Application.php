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

use Blend\Component\Application\Application as BaseApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Blend\Component\DI\Container;
use Psr\Log\LoggerInterface;
use Blend\Component\Configuration\Configuration;

/**
 * Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Application extends BaseApplication {

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $rootFolder;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Configuration
     */
    protected $config;

    public function __construct(Container $container
    , Configuration $config
    , LoggerInterface $logger
    , $rootFolder) {
        $this->container = $container;
        $this->rootFolder = $rootFolder;
        $this->config = $config;
        $this->logger = $logger;
    }

    protected function finalize(Request $request, Response $response) {
        
    }

    protected function handleRequest(Request $request) {
        
    }

    protected function handleRequestException(\Exception $ex, Request $request) {
        
    }

    protected function initialize(Request $request) {
        
    }

}
