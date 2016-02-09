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

/**
 * Template base class for a template
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Template {

    protected $templateFile;
    protected $context;

    public function __construct($templateFile) {
        $this->context = [];
        $this->templateFile = dirname(__FILE__) . '/Resources/' . $templateFile;
    }

    protected function set($key, $value) {
        $this->context[$key] = $value;
    }

    public function render($toFile = null) {
        return render_php_template($this->templateFile, $this->context, $this->normalizePath($toFile));
    }

    private function normalizePath($path) {
        if (!is_null($path)) {
            return str_replace('\\', '/', $path);
        }
        return $path;
    }

}
