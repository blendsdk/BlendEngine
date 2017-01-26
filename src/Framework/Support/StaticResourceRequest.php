<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Support;

/**
 * StaticResourceRequest is used to handle web requests to serve static
 * resources in development mode.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class StaticResourceRequest
{
    private $rootFolder;
    private $types;
    private $currentResource;

    public function __construct($rootFolder)
    {
        $this->rootFolder = $rootFolder;
        $this->types = array(
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'pdf' => 'application/pdf',
            'svg' => 'svg+xml',
            'woff' => 'application/font-woff',
            'woff2' => 'application/font-woff2',
            'eot' => 'application/vnd.ms-fontobject',
            'ttf' => 'font/opentype',
        );
    }

    public function isValid()
    {
        $uri = $uri = trim(strtok($_SERVER['REQUEST_URI'], '?'));
        $this->currentResource = realpath($this->rootFolder.'/'.$uri);

        return file_exists($this->currentResource) === true && is_file($this->currentResource);
    }

    public function serveLocalResource()
    {
        header('Content-Type: '.$this->getFileMIMEType());
        echo file_get_contents($this->currentResource);
    }

    private function getFileMIMEType()
    {
        $pathInfo = pathinfo($this->currentResource);
        $extension = $pathInfo['extension'];
        if (isset($this->types[$extension])) {
            return $this->types[$extension];
        } else {
            return 'text/plain';
        }
    }
}
