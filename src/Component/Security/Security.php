<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Security;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Security
{
    const ACCESS_PUBLIC = 10;
    const ACCESS_AUTHORIZED_USER = 20;
    const ACCESS_GUEST_ONLY = 30;
    const SECURITY_TYPE_API = 10;
    const SECURITY_TYPE_LOGIN = 20;
    const AUTHENTICATED_USER = '_auth_user';
}
