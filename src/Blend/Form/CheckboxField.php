<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Form;

use Blend\Form\Field;

/**
 * Description of CheckboxField
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CheckboxField extends Field {

    public function __construct($label) {
        parent::__construct('text', $label);
    }

}
