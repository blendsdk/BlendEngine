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

use Blend\Component\Filesystem\Filesystem;

/**
 * ModelBuilderConfig base class for a ModelBuilder Configuration
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ModelBuilderConfig {

    protected $projectFolder;
    protected $targetRootFolder;

    public abstract function getApplicationNamespace();

    public abstract function getModelRootNamespace();

    public abstract function getSchemaListToGenerate();

    public function __construct($projectFolder) {
        $this->projectFolder = $projectFolder;
        $this->targetRootFolder = $projectFolder . '/src/' . $this->getModelRootNamespace();
        $fs = new Filesystem();
        $fs->ensureFolder($this->targetRootFolder);
    }

    public function getTargetRootFolder() {
        return $this->targetRootFolder;
    }

}
