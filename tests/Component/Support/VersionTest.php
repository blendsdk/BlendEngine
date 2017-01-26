<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Support;

use Blend\Component\Support\Version;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function testSanity()
    {
        $v1 = new Version();
        $this->assertEquals($v1->getVersion(), '0.0.0');

        $v2 = new Version('');
        $this->assertEquals($v2->getVersion(), '0.0.0');

        $v3 = new Version('1');
        $this->assertEquals($v3->getVersion(), '1.0.0');

        $v4 = new Version('v1');
        $this->assertEquals($v4->getVersion(), 'v1.0.0');

        $v5 = new Version('0.2');
        $this->assertEquals($v5->getVersion(), '0.2.0');

        $v6 = new Version('v0.0.3');
        $this->assertEquals($v6->getVersion(), 'v0.0.3');

        $v7 = new Version('0.0.1-alpha');
        $this->assertEquals($v7->getVersion(), '0.0.1-alpha');

        $v8 = new Version('0.0.1-beta');
        $v8->bumpMajor()
           ->bumpMinor()
           ->bumpBuild();

        $this->assertEquals($v8->getVersion(), '1.1.1');

        $v9 = new Version('0');
        $v9->serReleaseTag('test');
        $this->assertEquals($v9->getVersion(), '0.0.0-test');
    }
}
