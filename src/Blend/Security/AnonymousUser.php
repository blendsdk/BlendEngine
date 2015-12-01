<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Security;

use Blend\Security\IUser;

/**
 * Provides an AnonymousUser for the application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class AnonymousUser implements IUser {

    public function getIdentifier() {
        return null;
    }

    public function getUsername() {
        return null;
    }

    public function isAuthenticated() {
        return false;
    }

    public function hasRole($role) {
        return false;
    }

}
