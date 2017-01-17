<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database\Factory\Converter;

/**
 * Description of ObjectConverter.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ObjectConverter implements IConverter
{
    public function toDbRecord($value)
    {
        return base64_encode(serialize($value));
    }

    public function toModel($value)
    {
        return unserialize(base64_decode($value));
    }
}
