<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Translation\Stubs;

use Blend\Component\Configuration\Configuration;
use Blend\Component\DI\Container;
use Blend\Framework\Application\ApplicationTestable;

/**
 * Description of TestableApplication.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TestableApplication extends ApplicationTestable
{
    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->container->get(Configuration::class);
    }

    protected function confiureServices(\Blend\Component\DI\ServiceContainer $container)
    {
        $container->loadServices(array(
        ));
    }
}
