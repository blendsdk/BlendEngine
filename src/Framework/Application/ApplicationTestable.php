<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Application;

use Blend\Component\Routing\Route;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Specialized Application class to be used in BlendEngine's unit testing
 * process.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ApplicationTestable extends Application
{
    public function loadServices($services)
    {
        $this->container->loadServices($services);
    }

    public function reInstallEventSubscribers()
    {
        $this->installEventSubscribers();
    }

    protected function installEventSubscribers()
    {
        $subscribers = $this->container->getByInterface(EventSubscriberInterface::class);
        foreach ($subscribers as $subscriber) {
            $this->dispatcher->removeSubscriber($subscriber);
            $this->dispatcher->addSubscriber($subscriber);
        }
    }

    /**
     * @return \Blend\Component\DI\ServiceContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function addRoute($name, $path, array $defaults = array(), array $requirements = array(), array $options = array(), $host = '', $schemes = array(), $methods = array(), $condition = '')
    {
        $this->routeCollection->add($name, new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition));
    }
}
