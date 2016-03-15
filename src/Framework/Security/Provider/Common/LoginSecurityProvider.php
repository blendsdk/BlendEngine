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

use Blend\Framework\Security\SecurityProviderInterface;
use Blend\Component\Routing\Route;
use Blend\Component\Configuration\Configuration;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Description of LoginSecurityProvider
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class LoginSecurityProvider implements SecurityProviderInterface {

    /**
     * @var Configuration;
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    protected abstract function getLoginURL();

    protected abstract function getLogoutURL();

    protected abstract function getEntryPointURL();

    protected abstract function getAfterLogoutURL();

    public function __construct(
    Configuration $config
    , Request $request
    , UrlGeneratorInterface $urlGenerator) {

        $this->config = $config;
        $this->request = $request;
        $this->urlGenerator = $urlGenerator;
    }

    public function delegateToEntryPoint() {
        return new RedirectResponse($this->getEntryPointURL());
    }

    public function getHandlerType() {
        return Route::SECURITY_TYPE_LOGIN;
    }

    public function startAuthentication() {
        return new RedirectResponse($this->getLoginURL());
    }

}
