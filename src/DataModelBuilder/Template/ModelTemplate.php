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
class ModelTemplate extends ClassTemplate {

    public function __construct() {
        parent::__construct('model.php');
    }

    /**
     * @param type $value
     * @return \Blend\DataModelBuilder\Template\ModelTemplate
     */
    public function setFQRN($value) {
        $this->set('fqrn', $value);
        return $this;
    }

    /**
     * @param type $name
     * @param type $type
     * @return \Blend\DataModelBuilder\Template\ModelTemplate
     */
    public function addProperty($name, $type = 'mixed') {
        $data = [
            'name' => $name,
            'getter' => 'get' . str_identifier($name),
            'setter' => 'set' . str_identifier($name),
            'type' => $type
        ];
        $this->set('props', $data, true);
        return $this;
    }

}
