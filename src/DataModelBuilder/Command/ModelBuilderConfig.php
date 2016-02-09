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

    /**
     * Should return the root namespace of your application
     */
    public abstract function getApplicationNamespace();

    /**
     * Should resturn the root namespace of your DAL (Data Access Layer)
     * "Database" for example
     */
    public abstract function getModelRootNamespace();

    /**
     * Should return aa string array of schemas to generate otherwise it should
     * return "null" to generate all the schemas
     */
    public abstract function getSchemaListToGenerate();

    /**
     * Should return a string array of relation (tables and views) names that
     * you are going to customize
     */
    public abstract function getCustomizedRelationList();

    /**
     * Should return the local date format, for example
     * return [
     *      'date' => 'd-m-Y',
     *      'time' => 'H:i:s',
     *      'datetime' => 'd-m-Y H:i:s'
     * ]
     */
    public abstract function getLocalDateTimeFormat();

    public function __construct($projectFolder) {
        $this->projectFolder = $projectFolder;
        $this->targetRootFolder = $projectFolder . '/src';
        $fs = new Filesystem();
        $fs->ensureFolder($this->targetRootFolder);
    }

    /**
     * Gets the root folder where DAL files will be generated
     * @return type
     */
    public function getTargetRootFolder() {
        return $this->targetRootFolder;
    }

}
