<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Security;

use Blend\Data\DatabaseService;

/**
 * Description of IUserManagerService
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class UserManagerService extends DatabaseService {

    protected abstract function createUserInstance($parameters);

    public abstract function authenticate($parameters);
}
