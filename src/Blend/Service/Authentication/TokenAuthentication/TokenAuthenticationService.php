<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Service\Authentication\TokenAuthentication;

use Blend\Service\Authentication\AuthenticationService;
use Symfony\Component\HttpFoundation\Request;
use Blend\Service\Authentication\TokenAuthentication\TokenAuthenticationForm;

/**
 * Base class for token based authentication
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class TokenAuthenticationService extends AuthenticationService {

    protected function createForm(Request $request) {
        return new TokenAuthenticationForm($request);
    }

}
