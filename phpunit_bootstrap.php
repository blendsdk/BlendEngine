<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$autoloadPath = null;
$paths = array(
    dirname(__DIR__) . '/vendor/autoload.php',
    dirname(__DIR__) . '/../../vendor/autoload.php',
);

foreach ($paths as $path) {
    if (file_exists($path)) {
        $autoloadPath = $path;
    }
}

include $autoloadPath;