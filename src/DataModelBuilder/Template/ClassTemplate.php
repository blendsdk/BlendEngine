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

use Blend\DataModelBuilder\Template\Template;

/**
 * Description of ClassTemplate
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ClassTemplate extends Template{

    /**
     * @param type $value
     * @return \Blend\DataModelBuilder\Template\ClassTemplate
     */
    public function setNamespace($value) {
        $this->set('namespace', $value);
        return $this;
    }

    /**
     * @param type $value
     * @return \Blend\DataModelBuilder\Template\ClassTemplate
     */
    public function setClassname($value) {
        $this->set('className', $value);
        return $this;
    }

    /**
     * @param type $value
     * @return \Blend\DataModelBuilder\Template\ClassTemplate
     */
    public function setBaseClass($value) {
        $this->set('baseClass', $value);
        return $this;
    }

    /**
     * @param type $value
     * @return \Blend\DataModelBuilder\Template\ClassTemplate
     */
    public function setClassModifier($value) {
        $this->set('classModifier', $value);
        return $this;
    }

}
