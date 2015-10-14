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

use Blend\Form\Form;

/**
 * Description of TokenAuthenticationForm
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TokenAuthenticationForm extends Form {

    protected function validate() {
        return true;
    }

    public function getToken() {
        return $this->request->request->get('token');
    }

}
