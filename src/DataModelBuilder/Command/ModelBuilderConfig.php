<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Command;

/**
 * ModelBuilderConfig base class for a ModelBuilder Configuration
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ModelBuilderConfig {

    protected $projectFolder;

    public abstract function getApplicationNamespace();

    public abstract function getModelRootNamespace();

    public function __construct($projectFolder) {
        $this->projectFolder = $projectFolder;
    }

}
