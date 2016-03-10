<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Support;

/**
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface RuntimeProviderInterface {

    public function getApplicationName();

    public function getAppRootFolder();

    public function getAppCacheFolder();

    public function set($key, $value);

    public function get($key, $default = null);

    public function isDebug();
}
