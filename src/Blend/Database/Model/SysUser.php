<?php

namespace Blend\Database\Model;

use Blend\Database\Model\Base\SysUser as Base;
use Blend\Security\IUser;

class SysUser extends Base implements IUser {

    public function getIdentifier() {
        return $this->getUserId();
    }

    public function isAuthenticated() {
        return $this->getUserId() !== null;
    }

}
