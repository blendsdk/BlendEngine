<?php

namespace Blend\Framework\Console\Command\Orm;

use Blend\Framework\Console\Command\Orm\ClassTemplate;

class ModelClassTemplate extends ClassTemplate
{

    public function __construct(array $data = array())
    {
        parent::__construct($data);
        $this->setValue('props', array());
        $this->setValue('generate', true);
    }

    public function addProperty($name, $type)
    {
        $props = $this->getValue('props');
        $props[] = array(
            'name' => $name,
            'type' => $type,
            'getter' => 'get' . str_identifier($name),
            'setter' => 'set' . str_identifier($name)
        );
        $this->setValue('props', $props);
    }

    public function setClassName($value)
    {
        parent::setClassName($value . 'Model');
    }

    public function setClassNamespace($value)
    {
        parent::setClassNamespace($value . '\\Model');
    }

    protected function getTemplateFile()
    {
        return 'model.php';
    }
}
