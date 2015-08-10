<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Security;

use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * Implements the RedirectableUrlMatcherInterface for Silex.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SecurityUrlMatcher extends UrlMatcher {

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo) {
        return $this->matchCollection(rawurldecode($pathinfo), $this->routes) !== null;
    }

}
