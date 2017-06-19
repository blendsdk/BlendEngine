<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Console\Command\Orm;

class ModelClassTemplate extends ClassTemplate {

    public function __construct(array $data = array()) {
        parent::__construct($data);
        $this->setValue('props', array());
        $this->setValue('generate', true);
    }

    public function setExtensionClass($value) {
        $this->setValue('extensionClass', $value);
    }

    public function addProperty($name, $type) {
        $props = $this->getValue('props');
        $props[] = array(
            'name' => $name,
            'type' => $type,
            'getter' => 'get' . str_identifier($name),
            'setter' => 'set' . str_identifier($name),
        );
        $this->setValue('props', $props);
    }

    public function setClassName($value) {
        parent::setClassName($value . 'Model');
    }

    public function setClassNamespace($value) {
        parent::setClassNamespace($value . '\\Model');
    }

    protected function getTemplateFile() {
        return 'model.php';
    }

}
