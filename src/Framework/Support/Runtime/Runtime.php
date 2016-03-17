<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Support\Runtime;

use Blend\Component\DI\Container;
use Blend\Framework\Support\Runtime\RuntimeProviderInterface;
use Blend\Framework\Security\User\Guest;
use Symfony\Component\HttpFoundation\Request;
use Blend\Framework\Security\User\UserProviderInterface;
use Blend\Component\Security\Security;

/**
 * Runtime is essencially a wrapper around the Container that can be used
 * to be customized and passed round to get and set domain (project)
 * specific settings
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Runtime implements RuntimeProviderInterface {

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Request;
     */
    protected $request;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->request = $this->container->get(Request::class);
    }

    public function getApplicationName() {
        return $this->name;
    }

    public function getAppRootFolder() {
        return $this->get('_app_root_folder');
    }

    public function getAppCacheFolder() {
        return $this->get('_app_cache_folder');
    }

    public function isDebug() {
        return $this->get('_debug');
    }

    public function set($key, $value) {
        $this->container->setScalar($key, $value);
    }

    public function get($key, $default = null) {
        if ($this->container->isDefined($key)) {
            return $this->container->get($key);
        } else {
            return $default;
        }
    }

    /**
     * Returns the current user, if no user is authenticated it will return
     * a Guest user
     * @return \Blend\Framework\Security\User\UserProviderInterface
     */
    public function getCurrentUser() {
        return $this->request->getSession()->get(Security::AUTHENTICATED_USER, new Guest());
    }

    public function setCurrentUser(UserProviderInterface $user) {
        $this->request->getSession()->set(Security::AUTHENTICATED_USER, $user);
        $this->set(Security::AUTHENTICATED_USER, $user);
    }

    /**
     * @return Container
     */
    public function getContainer() {
        return $this->container;
    }

}
