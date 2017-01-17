<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Exception;

/**
 * DatabaseQueryException represents an exception caused by incorrect SQL
 * statement or a database error.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DatabaseQueryException extends \Exception
{
    public static function createFromStatement($pdoStatement)
    {
        $errorInfo = $pdoStatement->errorInfo();

        return new self($errorInfo[2], 500);
    }
}
