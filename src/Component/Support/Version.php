<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Support;

/**
 * This class provides functionality to parse and handle a version string
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Version {

    private $prefix;
    private $major;
    private $minor;
    private $build;
    private $release;

    public function __construct($version_string = null) {
        $this->parse($version_string);
    }

    private function parse($source) {
        if (!empty($source)) {
            $temp = explode('.', strtolower(trim($source)));
            if (count($temp) != 0) {

                $this->prefix = $this->parsePrefix($temp[0]);
                $this->major = $this->makeNumber($temp[0]);

                if (isset($temp[1])) {
                    $this->minor = $this->makeNumber($temp[1], 0);
                }

                if (isset($temp[2])) {
                    $this->build = $this->makeNumber($temp[2], 0);
                    $this->release = $this->parsePostfix($temp[2]);
                }
            }
        }
    }

    /**
     * Bumps the major version
     * @return $this
     */
    public function bumpMajor() {
        $this->major += 1;
        return $this;
    }

    /**
     * Bumps the minor version
     * @return $this
     */
    public function bumpMinor() {
        $this->minor += 1;
        return $this;
    }

    /**
     * Bumps the build version
     * @return $this
     */
    public function bumpBuild() {
        $this->build += 1;
        return $this;
    }

    /**
     * Sets the release tag name
     * @param type $tag
     */
    public function serReleaseTag($tag) {
        $this->release = $tag;
    }

    private function makeNumber($data, $default = 0) {
        $data = $this->clean($data);
        if (!is_numeric($data)) {
            return $default;
        } else {
            return intval($data);
        }
    }

    private function parsePostfix($data) {
        if (!empty($data)) {
            $p = explode('-', $data);
            unset($p[0]);
            return implode('-', $p);
        } else {
            return null;
        }
    }

    private function parsePrefix($data) {
        if (isset($data[0]) && $data[0] == 'v') {
            return 'v';
        } else {
            return null;
        }
    }

    private function clean($data) {
        return preg_replace('/[^0-9,]|,[0-9]*$/', '', $data);
    }

    public function getVersion() {
        $result = array();

        if (!empty($this->major)) {
            $result[] = $this->major;
        } else {
            $result[] = 0;
        }

        if (!empty($this->minor)) {
            $result[] = $this->minor;
        } else {
            $result[] = 0;
        }

        if (!empty($this->build)) {
            $result[] = $this->build;
        } else {
            $result[] = 0;
        }

        $result = implode('.', $result);

        if (!empty($this->prefix)) {
            $result = 'v' . $result;
        }

        if (!empty($this->release)) {
            $result = $result . '-' . $this->release;
        }

        return $result;
    }

    public static function FromFile($filename) {
        return new Version(file_get_contents($filename));
    }

}

class VersionException extends \Exception {

}
