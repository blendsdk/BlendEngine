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

    private $cachedProjectFolder = null;

    /**
     * Returns the folder where this project is located. The location
     * is calculated by returning the parent folder of the OS script
     * where this class is instantiated from
     * @return string The project folder or null
     */
    protected function getProjectFolder() {
        if (is_null($this->cachedProjectFolder)) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            foreach ($backtrace as $item) {
                $file = $item['file'];
                if (stripos($file, 'blend.php') !== false) {
                    $this->cachedProjectFolder = realpath(dirname($file)
                            . DIRECTORY_SEPARATOR
                            . '..'
                            . DIRECTORY_SEPARATOR);
                }
            }
        }
        return $this->cachedProjectFolder;
    }

}
