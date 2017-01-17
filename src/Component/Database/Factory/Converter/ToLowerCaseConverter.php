<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Database\Factory\Converter;

/**
 * Description of ToLowerCaseConverter.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ToLowerCaseConverter implements IConverter
{
    public function toModel($value)
    {
        return strtolower($value);
    }

    public function toDbRecord($value)
    {
        return strtolower($value);
    }
}
