<?php

namespace Blend\Framework\Console\Command\Orm;

use Blend\Framework\Console\Command\Orm\Template;

abstract class ClassTemplate extends Template
{

    public function setClassModifier($value)
    {
        $this->setValue('classModifier', $value);
    }

    public function setClassNamespace($value)
    {
        $this->setValue('classNamespace', $value);
    }

    public function addUses($value)
    {
        $this->setValue('uses', $value);
    }

    public function setApplicationNamespace($value)
    {
        $this->setValue('appNamespace', $value);
    }

    public function setClassName($value)
    {
        $this->setValue('className', $value);
    }

    public function setFQRN($value)
    {
        $this->setValue('classFQRN', $value);
    }

    public function setBaseClassName($value)
    {
        $this->setValue('classBaseClass', $value);
    }

    public function renderToFile($filename)
    {
        $templateFile = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->getTemplateFile();
        render_php_template($templateFile, $this->getData(), $filename);
    }
}
