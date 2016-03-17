<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Security\Provider\Common;

use Blend\Component\Routing\Route;
use Blend\Framework\Security\Provider\SecurityProvider;
use Symfony\Component\HttpFoundation\Request;
use Blend\Component\Security\Security;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class LoginSecurityProvider extends SecurityProvider {

    const REFERER_URL = '_referer_url';

    public function getHandlerType() {
        return Security::SECURITY_TYPE_LOGIN;
    }

    public function handle($accessMethod, Route $route) {

    }

    /**
     * Tries to get the current user from the container
     * @return null|User\UserProviderInterface
     */
    protected function getCurrentUser() {
        return $this->request->getSession()->get(Security::AUTHENTICATED_USER, new Guest());
    }

    protected function saveReferer() {
        $referer = $this->request->getUri();
        $this->request->getSession()->set(self::REFERER_URL
                , $this->request->getUri());
    }

    protected function getReferer() {
        $session = $this->request->getSession();
        $result = $session->get(self::REFERER_URL, null);
        if ($result !== null) {
            $session->remove(self::REFERER_URL);
        }
        return $result;
    }

}
