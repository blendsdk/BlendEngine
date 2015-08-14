<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Core;

/**
 * Allowed MimeType types for static files
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class MimeTypes {

    private static $types = array(
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
        'ttf' => 'font/opentype'
    );

    public static function getFileMIMEType($filename) {
        $pathInfo = pathinfo($filename);
        $extension = $pathInfo['extension'];
        if (isset(self::$types[$extension])) {
            return self::$types[$extension];
        } else {
            return 'text/plain';
        }
    }

}
