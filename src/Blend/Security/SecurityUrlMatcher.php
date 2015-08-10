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

use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * Implements the SecurityUrlMatcher for BlendEngine security model.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SecurityUrlMatcher extends UrlMatcher {

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo) {
        return $this->matchCollection(rawurldecode($pathinfo), $this->routes) !== null;
    }

}
