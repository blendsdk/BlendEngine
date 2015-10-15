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

use Blend\Database\Schema\SYS_USER_SCHEMA as SC;
use Blend\Database\InsertStatement;

/**
 * Description of CreateNewUserStatement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CreateNewUserStatement extends InsertStatement {

    public function __construct() {
        parent::__construct(SC::TABLE_NAME);
    }

    public function setUsername($value) {
        $this->setParameterValue(SC::USERNAME, $value);
    }

    public function setPassword($value) {
        $this->setParameterValue(SC::PASSWORD, $value);
    }

    public function setEmail($value) {
        $this->setParameterValue(SC::USER_EMAIL, $value);
    }

    public function setIsActive($value) {
        $this->setParameterValue(SC::USER_IS_ACTIVE, $value);
    }

    protected function getInsertColumns() {
        return array(
            SC::USERNAME,
            SC::PASSWORD,
            SC::USER_EMAIL,
            SC::USER_IS_ACTIVE
        );
    }

}
