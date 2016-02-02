<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Console;

use Symfony\Component\Console\Application as ApplicationBase;

/**
 * Description of Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Application extends ApplicationBase {

    private $projectFolder;

    public function __construct($script_dir, $name = 'UNKNOWN', $version = 'UNKNOWN') {
        parent::__construct($name, $version);
        $this->projectFolder = realpath($script_dir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
    }

    /**
     * Returns the folder where this project is located. The location
     * is calculated by returning the parent folder of the OS script
     * where this class is instantiated from
     * @return string The project folder or null
     */
    public function getProjectFolder() {
        return $this->projectFolder;
    }

}
