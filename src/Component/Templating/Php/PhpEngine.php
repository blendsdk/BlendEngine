<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Templating\Php;

use Blend\Component\Templating\TemplateEngineInterface;

/**
 * Basic PHP PhpEngine.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class PhpEngine implements TemplateEngineInterface
{
    /**
     * @var bool
     */
    protected $trimOutput;
    protected $defaults;

    public function __construct($trimOutput = true, array $defaults = array())
    {
        $this->trimOutput = $trimOutput;
        $this->defaults = $defaults;
    }

    public function render($view, array $parameters = array())
    {
        return render_php_template($view, $this->normalizeParameters($parameters), null, $this->trimOutput
        );
    }

    protected function normalizeParameters(array $parameters = array())
    {
        return array_merge($this->defaults, $parameters);
    }

    public function setViewPaths(array $paths = array())
    {
        return false;
    }
}
