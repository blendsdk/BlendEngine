<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Model;

use Blend\Model\FieldProvider;

/**
 * Generic Model class
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Model extends FieldProvider {

    /**
     * returns a NotBlank validator
     * @return validator
     */
    protected function validateNotBlank() {
        return array('notBlankValidator', array());
    }

    /**
     * Checks if a given value is empty
     * @param type $value
     * @param type $label
     * @return boolean
     */
    protected function notBlankValidator($value, $label) {
        if (empty($value)) {
            return "{$label} cannot be empty!";
        } else {
            return true;
        }
    }

}
