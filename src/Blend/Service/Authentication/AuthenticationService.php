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
use Blend\Form\Form;
use Blend\Core\Application;
use Blend\Security\UserManagerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Base class for all AuthenticationServices
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class AuthenticationService extends Service {

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var UserManagerService
     */
    protected $ummService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var FlashBagInterface
     */
    protected $flashBag;

    protected abstract function createForm(Request $request);

    protected abstract function getUser();

    public function __construct(Application $application, UserManagerService $ummService) {
        parent::__construct($application);
        $this->ummService = $ummService;
    }

    /**
     * @return Form;
     */
    public function getForm() {
        return $this->form;
    }

    public function getErrors() {
        return $this->flashBag->get(__CLASS__);
    }

    public function validateRequest(Request $request) {
        $this->form = $this->createForm($request);
        $this->flashBag = $request->getSession()->getFlashBag();
        return $this->form->isSubmitted();
    }

    public function authenticate() {
        if ($this->form->isSubmitted()) {
            $user = $this->getUser();
            if (is_null($user)) {
                $this->flashBag->add(__CLASS__, 'error.invalid.username.password');
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
