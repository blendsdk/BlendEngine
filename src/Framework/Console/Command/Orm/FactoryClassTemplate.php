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

class FactoryClassTemplate extends ClassTemplate
{
    public function __construct(array $data = array())
    {
        parent::__construct($data);
        $this->setValue('methods', array());
    }

    public function addMethod(Method $method)
    {
        $methods = $this->getValue('methods');
        $methods[] = $method->getData();
        $this->setValue('methods', $methods);
    }

    public function setClassName($value)
    {
        parent::setClassName($value . 'Factory');
    }

    public function setClassNamespace($value)
    {
        $value = $this->getValue('appNamespace') . '\\' . $value;
        parent::setClassNamespace($value . '\\Factory');
        $this->setValue('modelNamespace', $value . '\\Model');
    }

    public function setModelClass($value)
    {
        $this->setValue('modelClass', $value . 'Model');
        $uses = $this->getValue('uses');
        $uses[] = $this->getValue('modelNamespace') . '\\' . $this->getValue('modelClass');
        $this->addUses($uses);
    }

    protected function getTemplateFile()
    {
        return 'factory.php';
    }
}
