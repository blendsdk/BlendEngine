<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Security\User;

/**
 * Guest.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Guest implements UserProviderInterface
{
    public function getEmail()
    {
        return null;
    }

    public function getPermissions()
    {
        return array();
    }

    public function getRoles()
    {
        return array();
    }

    public function getUserID()
    {
        return null;
    }

    public function getUsername()
    {
        return null;
    }

    public function hasPermission($permission)
    {
        return false;
    }

    public function hasRole($role)
    {
        return false;
    }

    public function isActive()
    {
        return false;
    }

    public function isGuest()
    {
        return true;
    }

    public function getAPIKey()
    {
        return null;
    }
}
