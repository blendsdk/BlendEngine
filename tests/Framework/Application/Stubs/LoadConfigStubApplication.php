<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Application\Stubs;

use Blend\Framework\Application\Application;
use Blend\Component\Configuration\Configuration;

/**
 * Description of LoadConfigStubApplication
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class LoadConfigStubApplication extends Application {

    public function run(\Symfony\Component\HttpFoundation\Request $request = null) {
        $this->loadConfiguration();
    }

    public function testGetConfigValue($key, $default = null) {
        return $this->container->get(Configuration::class)->get($key, $default);
    }

}
