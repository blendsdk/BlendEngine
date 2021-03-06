<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Support\Runtime;

use Blend\Component\DI\Container;
use Blend\Component\Security\Security;
use Blend\Framework\Security\User\Guest;
use Blend\Framework\Security\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Runtime is essentially a wrapper around the Container that can be used
 * to be customized and passed round to get and set domain (project)
 * specific settings.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Runtime implements RuntimeProviderInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->request = $this->container->get(Request::class);
    }

    public function getApplicationName()
    {
        return $this->name;
    }

    public function getAppRootFolder()
    {
        return $this->get(RuntimeAttribute::APPLICATION_ROOT_FOLDER);
    }

    public function getAppCacheFolder()
    {
        return $this->get(RuntimeAttribute::APPLICATION_CACHE_FOLDER);
    }

    public function isDebug()
    {
        return $this->get(RuntimeAttribute::DEBUG);
    }

    public function set($key, $value)
    {
        $this->container->setScalar($key, $value);
    }

    public function get($key, $default = null)
    {
        if ($this->container->isDefined($key)) {
            return $this->container->get($key);
        } else {
            return $default;
        }
    }

    /**
     * Returns the current user, if no user is authenticated it will return
     * a Guest user.
     *
     * @return \Blend\Framework\Security\User\UserProviderInterface
     */
    public function getCurrentUser()
    {
        if ($this->request && $this->request->getSession()) {
            return $this->request->getSession()->get(Security::AUTHENTICATED_USER, new Guest());
        } else {
            return new Guest();
        }
    }

    /**
     * The the current user of this runtime envirounment (application).
     *
     * @param UserProviderInterface $user
     */
    public function setCurrentUser(UserProviderInterface $user)
    {
        $this->request->getSession()->set(Security::AUTHENTICATED_USER, $user);
        $this->set(Security::AUTHENTICATED_USER, $user);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Signout from the application by clearing the session.
     *
     * @return RedirectResponse
     */
    public function signOut()
    {
        if ($this->request) {
            $this->request->getSession()->clear();

            return new RedirectResponse($this->request->getUri());
        }

        return null;
    }

    /**
     * Returns an instance of the current request.
     *
     * @return type
     */
    public function getRequest()
    {
        return $this->request;
    }
}
