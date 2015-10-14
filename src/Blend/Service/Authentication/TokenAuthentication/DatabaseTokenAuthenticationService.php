<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Service\Authentication\TokenAuthentication;

use Blend\Core\Application;
use Blend\Service\Authentication\TokenAuthentication\TokenAuthenticationService;
use Blend\Security\Authentication\Methods\Database\UserManagerService;

/**
 * Description of DatabaseTokenAuthenticationService
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DatabaseTokenAuthenticationService extends TokenAuthenticationService {

    public function __construct(Application $application, UserManagerService $ummService = null) {
        if (is_null($ummService)) {
            $ummService = new UserManagerService($application->getDatabase());
        }
        parent::__construct($application, $ummService);
    }

    protected function getUser() {
        return $this->ummService->authenticate($this->form->getToken());
    }

}
