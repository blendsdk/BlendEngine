<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Service\UserManager\Database;

use Blend\Database\SelectStatement;
use Blend\Database\Schema\SYS_USER_SCHEMA;

/**
 * SelectUserByTokenHashStatement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SelectUserByTokenHashStatement extends SelectStatement {

    public function setToken($token) {
        $this->setParameterValue('token', $token);
    }

    protected function buildSQL() {
        $tmpl = "SELECT * FROM %s WHERE md5(%s||%s) = :token AND %s = true";
        return sprintf($tmpl, SYS_USER_SCHEMA::TABLE_NAME, SYS_USER_SCHEMA::USERNAME, SYS_USER_SCHEMA::PASSWORD, SYS_USER_SCHEMA::USER_IS_ACTIVE
        );
    }

}
