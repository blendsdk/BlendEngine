<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Service\ControllerHandler;

use Blend\Component\DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Blend\Framework\Service\ControllerHandler\ControllerHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * ControllerResolverService
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ControllerHandler implements ControllerHandlerInterface {

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(Container $container, LoggerInterface $logger) {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function handle(Request $request, array $matchedRoute) {
        $this->assertControllerKey($request, $matchedRoute);
        $controller = $matchedRoute['_controller'];
        if ($this->isArrayDefinition($controller)) {
            return $this->container->call($controller[0], $controller[1], $matchedRoute);
        } else {
            $error = "The _controller is has an invalid [controller,action] signature!" .
                    " You should check the Route creation!";
            $this->logger->error($error, ['matchedRoute' => $matchedRoute, $request->getPathInfo()]);
            throw new InvalidParameterException($error);
        }
    }

    /**
     * Check the signature of the provided _controller
     * @param array $controller
     * @return boolean
     */
    protected function isArrayDefinition($controller) {
        if (is_array($controller) && count($controller) == 2 && is_string($controller[0]) && is_string($controller[1])
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Assert is the matched route contains a _controller key/pare
     * @param Request $request
     * @param array $matchedRoute
     * @throws InvalidParameterException
     */
    protected function assertControllerKey(Request $request, array $matchedRoute) {
        if (!array_key_exists('_controller', $matchedRoute)) {
            $error = "The matched route does not have a [_controller] " .
                    "key/value pair. You should check the Route creation!";
            $this->logger->error($error, ['matchedRoute' => $matchedRoute, $request->getPathInfo()]);
            throw new InvalidParameterException($error);
        }
    }

}
