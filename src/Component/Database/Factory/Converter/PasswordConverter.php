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

use Blend\Component\Database\Factory\Converter\IConverter;

/**
 * Description of PasswordConverter
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class PasswordConverter implements IConverter {
    
    public function toDbRecord($value) {
        return sha1($value);
    }

    public function toModel($value) {
        return $value;
    }

}
