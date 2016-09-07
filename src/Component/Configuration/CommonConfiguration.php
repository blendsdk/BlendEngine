<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Configuration;

use Blend\Component\Configuration\CommonConfigurationInterface;

/**
 * Base class for creating a common implementation
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class CommonConfiguration implements CommonConfigurationInterface {

    private $rootFolder;

    public function __construct($_ROOT_FOLDER) {
        $this->rootFolder = $_ROOT_FOLDER;
    }

    public function getRootFolder() {
        return $this->rootFolder;
    }

}
