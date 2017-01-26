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

use Blend\Component\Database\Schema\Record;

abstract class Template extends Record
{
    abstract protected function getTemplateFile();

    public function renderToFile($filename)
    {
        $templateFile = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->getTemplateFile();
        render_php_template($templateFile, $this->getData(), $filename);
    }
}
