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

    public function __construct(Configuration $config
    , LoggerInterface $logger
    , $rootFolder) {
        date_default_timezone_set($config->get('timezone', 'UTC'));
        $config->mergeWith(['app.root.folder' => $rootFolder]);
        $this->container = new Container();
        $this->container->setScalars([
            LoggerInterface::class => $logger,
            Configuration::class => $config
        ]);
    }

    protected function finalize(Request $request, Response $response) {
        //
    }

    protected function handleRequest(Request $request) {
        //
    }

    protected function handleRequestException(\Exception $ex, Request $request) {
        //
    }

    protected function initialize() {
        //
    }

}
