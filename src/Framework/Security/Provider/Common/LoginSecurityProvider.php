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
use Blend\Component\Security\Security;
use Blend\Framework\Security\Provider\SecurityProvider;
use Blend\Framework\Security\User\Guest;
use Blend\Framework\Security\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class LoginSecurityProvider extends SecurityProvider
{
    const REFERER_URL = '_referer_url';

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    abstract protected function getLoginURL();

    abstract protected function getLogoutURL();

    abstract protected function getSecureEntryPointURL();

    public function __construct(
    Request $request, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($request);
        $this->urlGenerator = $urlGenerator;
    }

    public function getHandlerType()
    {
        return Security::SECURITY_TYPE_LOGIN;
    }

    public function handle($accessMethod, Route $route)
    {
        $user = $this->getCurrentUser();

        if ($accessMethod === Security::ACCESS_AUTHORIZED_USER) {
            if ($user->isGuest()) {
                $this->saveReferer();

                return $this->redirectTo($this->getLoginURL());
            }
        } elseif ($accessMethod === Security::ACCESS_GUEST_ONLY) {
            if (!$user->isGuest()) {
                $referer = $this->getReferer();
                if ($referer !== null) {
                    return $this->redirectTo($referer);
                } else {
                    return $this->redirectTo($this->getSecureEntryPointURL());
                }
            }
        }
    }

    /**
     * Redirect the user to a given URL.
     *
     * @param type $url
     *
     * @return RedirectResponse
     */
    protected function redirectTo($url)
    {
        return new RedirectResponse($url);
    }

    /**
     * Tries to get the current user from the container.
     *
     * @return null|UserProviderInterface
     */
    protected function getCurrentUser()
    {
        return $this->request->getSession()->get(Security::AUTHENTICATED_USER, new Guest());
    }

    /**
     * The the current URI as the referer for the next request to handle
     * If the referer is the login or the logout url we do not save anything
     * since the auto redirect mechanism will go into a loop.
     */
    protected function saveReferer()
    {
        $current = $this->request->getPathInfo();
        if ($current !== $this->getLoginURL() && $current !== $this->getLogoutURL()) {
            $this->request->getSession()->set(self::REFERER_URL, $this->request->getUri());
        }
    }

    /**
     * Get the previously aved referer.
     *
     * @return string
     */
    protected function getReferer()
    {
        $session = $this->request->getSession();
        $result = $session->get(self::REFERER_URL, null);
        if ($result !== null) {
            $session->remove(self::REFERER_URL);
        }

        return $result;
    }

    public function finalize($accessMethod, Route $route, Response $response)
    {
        return; //do nothing
    }
}
