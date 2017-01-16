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

use Blend\Framework\Security\User\UserProviderInterface;

/**
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface RuntimeProviderInterface {

    public function getRequest();

    public function getApplicationName();

    public function getAppRootFolder();

    public function getAppCacheFolder();

    public function set($key, $value);

    public function get($key, $default = null);

    public function isDebug();

    /**
     * @return \Blend\Component\DI\Container
     */
    public function getContainer();

    /**
     * @return UserProviderInterface
     */
    public function getCurrentUser();

    public function setCurrentUser(UserProviderInterface $user);

    /**
     * Signout by clearing the current session and return a redirect
     * response to the current request. This should trigger the security
     * handler to redirect this request to an authentication workflow
     * @return null|RedirectResponse
     */
    public function signOut();
}
