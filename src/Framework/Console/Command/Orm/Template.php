<?php

namespace Blend\Framework\Console\Command\Orm;

use Blend\Component\Database\Schema\Record;

abstract class Template extends Record
{

    protected abstract function getTemplateFile();

    public function renderToFile($filename)
    {
        $templateFile = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->getTemplateFile();
        render_php_template($templateFile, $this->getData(), $filename);
    }
}
