<?php

namespace Blend\Service\UserManager\Database\Model;

use Blend\Security\IUser;
use Blend\Service\UserManager\Database\Model\Base\SysUser as Base;

class SysUser extends Base implements IUser {

    private $roles = null;

    public function getIdentifier() {
        return $this->getUserID();
    }

    public function isAuthenticated() {
        return !is_null($this->getUserID()) && $this->getUserIsActive() === true;
    }

    public function hasRole($role) {
        if (is_null($this->roles)) {
            $this->roles = explode(',', $this->getUserRole());
        }
        return in_array($role, $this->roles);
    }

}
