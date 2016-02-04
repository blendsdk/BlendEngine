<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!ini_get('date.timezone')) {
    ini_set('date.timezone', 'Europe/Amsterdam');
}

foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
    if (file_exists($file)) {
        define('BLEND_COMPOSER_INSTALL', $file);
        break;
    }
}

unset($file);

if (!defined('BLEND_COMPOSER_INSTALL')) {
    fwrite(STDERR, 'You need to set up the project dependencies using the following commands:' . PHP_EOL .
            'wget http://getcomposer.org/composer.phar' . PHP_EOL .
            'php composer.phar install' . PHP_EOL
    );
    die(1);
}

require BLEND_COMPOSER_INSTALL;
