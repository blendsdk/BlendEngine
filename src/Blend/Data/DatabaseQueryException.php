<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Data;

/**
 * DatabaseQueryException
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DatabaseQueryException extends \Exception {

    public static function createFromStatement($pdoStatement) {
        $info = implode("\n", $pdoStatement->errorInfo());
        return new DatabaseQueryException($info, $pdoStatement->errorCode());
    }

}
