<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Templating;

/**
 * A simplified interface to implement templating engines.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface TemplateEngineInterface
{
    public function render($view, array $parameters = array());

    public function setViewPaths(array $paths = array());
}
