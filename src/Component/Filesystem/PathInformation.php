<?php

namespace Blend\Component\Filesystem;

use Blend\Component\Exception\InvalidOperationException;

/**
 * A wrapper class for the pathinfo utility
 */
class PathInformation
{
    private $pathinfo;

    public function __construct($path)
    {
        if (file_exists($path)) {
            $this->pathinfo = pathinfo($path);
        } else {
            throw new InvalidOperationException("[$path] dot not exist!");
        }
    }

    /**
     * Returns the directory name
     * @return string
     */
    public function getDirectoryName()
    {
        return $this->pathinfo['dirname'];
    }

    /**
     * Gets the filename including the extension
     * @return string
     */
    public function getBasename()
    {
        return $this->pathinfo['basename'];
    }

    /**
     * Gets the file extension
     * @return string
     */
    public function getExtension()
    {
        return $this->pathinfo['extension'];
    }

    /**
     * Gets the filename without the extension
     * @return string
     */
    public function getFilename()
    {
        return $this->pathinfo['filename'];
    }
}
