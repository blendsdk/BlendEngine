<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Service\Authentication;

use Blend\Service\Service;
use Blend\Core\Application;
use Blend\Service\UserManager\UserManagerService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class for all AuthenticationServices
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class AuthenticationService extends Service {

    /**
     * @var UserManagerService
     */
    protected $ummService;

    protected abstract function getUser();

    public function __construct(Application $application, UserManagerService $ummService) {
        parent::__construct($application);
        $this->ummService = $ummService;
    }

    public function authenticate() {
        if ($this->form->isSubmitted()) {
            $user = $this->getUser();
            if (is_null($user)) {
                $this->addError('error.invalid.username.password');
                return false;
            } else {
                $this->application->setUser($user);
                return true;
            }
        } else {
            return false;
        }
    }

}
