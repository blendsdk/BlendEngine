<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Exception;

/**
 * InvalidConfigException represents an exception caused by incorrect object
 * configuration.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class InvalidConfigException extends \Exception
{
    /**
     * @return string The name of this exception
     */
    public function getName()
    {
        return 'Invalid Configuration';
    }
}
