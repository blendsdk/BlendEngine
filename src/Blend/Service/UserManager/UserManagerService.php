<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Service\UserManager;

use Blend\Database\DatabaseService;

/**
 * Base class for all UserManagerServices
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class UserManagerService extends DatabaseService {

    public abstract function authenticate($parameters);
}
