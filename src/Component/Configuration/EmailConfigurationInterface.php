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
 * Interface for creating an email configuration
 * 
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface EmailConfigurationInterface {

    function getMailServerHost();

    function getMailServerPort();

    function getMailServerUsername();

    function getMailServerPassword();

    function getMailServerEncryptionType();

    function getMailServerAuthenticationMode();
}
