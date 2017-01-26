<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Filesystem;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem as FilesystemBase;

/**
 * This class provides various filesystem related functionality.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Filesystem extends FilesystemBase
{
    /**
     * Make sure the request folder exists*.
     *
     * @param string $folder The folder to check and create
     * @param int    $mode   The directory mode
     *
     * @return string The real ptah of the folder created
     */
    public function ensureFolder($folder, $mode = 0777)
    {
        if (!$this->exists($folder)) {
            $this->mkdir($folder, $mode);
        }
        if (is_array($folder)) {
            foreach ($folder as $key => $item) {
                $folder[$key] = realpath($item);
            }
        } else {
            return realpath($folder);
        }
    }

    /**
     * Asserts if a folder exists and it is writable.
     *
     * @param type $folder
     *
     * @return type
     *
     * @throws \Exception
     */
    public function assertFolderWritable($folder)
    {
        if (is_dir($folder) && is_writable($folder)) {
            return $folder;
        } else {
            throw new FileNotFoundException(
            "$folder does not exist or it is not writable", 500);
        }
    }
}
