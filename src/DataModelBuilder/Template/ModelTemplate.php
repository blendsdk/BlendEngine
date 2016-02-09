<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Template;

use Blend\DataModelBuilder\Template\ClassTemplate;

/**
 * Template for a Model class
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ModelTemplate extends ClassTemplate{

    public function __construct() {
        parent::__construct('model.php');
    }

}
