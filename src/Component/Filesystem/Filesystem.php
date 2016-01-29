<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Filesystem;

use Symfony\Component\Filesystem\Filesystem as FilesystemBase;

/**
 * This class provides various file system related functionality
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Filesystem extends FilesystemBase {

    /**
     * Make sure the request folder exists*
     * @param string $folder The folder to check and create
     * @param int $mode The directory mode
     * @return string The real ptah of the folder created
     */
    public function ensureFolder($folder, $mode = 0777) {
        if (!$this->exists($folder)) {
            $this->mkdir($folder, $mode);
        }
        return realpath($folder);
    }

}
