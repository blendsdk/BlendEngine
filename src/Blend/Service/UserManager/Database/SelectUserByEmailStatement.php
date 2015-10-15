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

use Blend\Database\Schema\SYS_USER_SCHEMA;
use Blend\Database\SelectStatement;

/**
 * Description of SelectUserByEmailStatement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SelectUserByEmailStatement extends SelectStatement {

    public function setEmail($email) {
        $this->setParameterValue('user_email', $email);
    }

    protected function buildSQL() {
        $sql = "SELECT * FROM %table% WHERE %email_field% = :user_email LIMIT 1";
        return str_replace_template($sql, array(
            '%table%' => SYS_USER_SCHEMA::TABLE_NAME,
            '%email_field%' => SYS_USER_SCHEMA::USER_EMAIL
        ));
    }

//put your code here
}
