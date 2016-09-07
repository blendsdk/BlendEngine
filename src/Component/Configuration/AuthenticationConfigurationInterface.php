<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Configuration;

/**
 * Interface for creating a configuration for the authentication module
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface AuthenticationConfigurationInterface {

    function getAuthenticationLoginPath();

    function getAuthenticationLogoutPath();

    function getAuthenticationAfterLogoutPath();
}
