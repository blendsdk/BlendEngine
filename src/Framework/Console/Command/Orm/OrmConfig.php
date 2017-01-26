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

/**
 * OrmConfig is used to create a configuration for the ORM builder command.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class OrmConfig
{
    private $outputFolder;
    private $rootFolder;
    private $tables;
    private $baseclass;

    public function __construct()
    {
        $this->tables = array();
        $this->baseclass = array(
            'factory' => array(),
            'model' => array()
        );
    }

    public function setRootFolder($folder)
    {
        $this->rootFolder = $folder;

        return $this;
    }

    public function addTable($table, $schema = 'public')
    {
        $this->tables[] = array($schema, $table);

        return $this;
    }

    public function setFactoryBaseClass($table, $class)
    {
        return $this->setBaseClass('factory', $table, $class);
    }

    public function setModelBaseClass($table, $class)
    {
        return $this->setBaseClass('model', $table, $class);
    }

    private function setBaseClass($type, $table, $class)
    {
        $this->baseclass[$type][$table] = $class;
        return $this;
    }

    public function getBaseClass($type, $table, $default)
    {
        if (isset($this->baseclass[$type][$table])) {
            $clazz = $this->baseclass[$type][$table];
        } else {
            $clazz = $default;
        }
        $classRef = new \ReflectionClass($clazz);
        return array('use' => $classRef->getName(), 'class_name' => str_replace($classRef->getNamespaceName() . '\\', '', $classRef->getName()));
    }

    public function getTables()
    {
        return $this->tables;
    }

    public function getOutputFolder()
    {
        return $this->rootFolder . DIRECTORY_SEPARATOR . $this->outputFolder . DIRECTORY_SEPARATOR . 'Database';
    }

    public function setOutputFolder($folder)
    {
        $this->outputFolder = $folder;

        return $this;
    }
}
