<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Security\Authentication\Forms;

use Blend\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Blend\Security\UserManagerService;

/**
 * Description of TokenAuthenticationForm
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TokenAuthenticationForm extends Form {

    /**
     * @var IUserManagerService;
     */
    protected $ummService;

    /**
     *
     * @var Blend\Security\IUser;
     */
    protected $user;

    public function __construct(Request $request, UserManagerService $ummService) {
        $this->user = null;
        $this->ummService = $ummService;
        parent::__construct($request);
    }

    protected function validate() {
        $token = $this->parameters->get('token');
        $user = $this->ummService->authenticate($token);
        if (!is_null($user)) {
            $this->user = $user;
        } else {
            $this->addError('error.invalid.username.password');
            $this->user = null;
        }
        return empty($this->user) === false;
    }

    /**
     * @return \Blend\Security\IUser
     */
    public function getAuthenticatedUser() {
        return $this->user;
    }

}
