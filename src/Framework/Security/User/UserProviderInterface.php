<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Security\User;

/**
 * Generic interface for implementing a User.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface UserProviderInterface
{
    public function getUserID();

    public function getAPIKey();

    public function getUsername();

    public function getEmail();

    public function getRoles();

    public function hasRole($role);

    public function getPermissions();

    public function hasPermission($permission);

    public function isActive();

    public function isGuest();
}
