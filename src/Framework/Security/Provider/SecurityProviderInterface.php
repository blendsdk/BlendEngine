<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Security\Provider;

use Blend\Component\Routing\Route;

/**
 * SecurityProviderInterface provides a common interface for both form based
 * and api key based security
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface SecurityProviderInterface {

    public function getHandlerType();

    public function handle($accessMethod, Route $route);
}
