<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Console\StatementConfig;

/**
 * Description of Statament
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class StatementConfig {

    /**
     * @var boolean
     */
    protected $overwrite;
    protected $namespace;
    protected $outfolder;
    protected $name;
    protected $uses;
    protected $setters;
    protected $description;

    public function getOutFolder() {
        return $this->outfolder;
    }

    public function getName() {
        return $this->name;
    }

    public function overwrite() {
        return $this->overwrite;
    }

    public function getNamespace() {
        return $this->namespace;
    }

    public function getUses() {
        return $this->uses;
    }

    public function setDescription($text) {
        $this->description = $text;
    }

    public function getDescription() {
        return is_null($this->description) ? $this->getName() : $this->description;
    }

    public function addUse($use) {
        if (!is_array($use)) {
            $use = array($use);
        }
        $this->uses = array_merge($this->uses, $use);
    }

    public function addSetter($name, $type = 'mixed', $param = null) {
        $this->setters[] = array('name' => $name, 'type' => $type, 'param' => is_null($param) ? $name : $param);
    }

    public function getSetters() {
        return $this->setters;
    }

    public function __construct($name, $namespace, $outfolder, $description = null, $overwrite = true) {
        $this->overwrite = $overwrite;
        $this->namespace = $namespace;
        $this->outfolder = $outfolder;
        $this->description = $description;
        $this->name = $name;
        $this->uses = array();
        $this->setters = array();
    }

    protected function ucWords($string, $prefix = '', $postfix = '') {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        return "{$prefix}{$str}{$postfix}";
    }

}
