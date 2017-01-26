<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\ProjectSetup;

use Blend\Tests\ProjectUtil;

/**
 * SetupApplicationTest.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SetupApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testSanity()
    {
        $projectFolder = ProjectUtil::createNewProject('sanity', true);
        $this->assertFileExists("$projectFolder/bin/sanity.php");
        $this->assertFileExists("$projectFolder/web/css/sanity.css");
        $commandTester = ProjectUtil::runCommand($projectFolder, 'list', array('-V --no-ansi'), 'Sanity\Console\SanityApplication');
        $display = preg_replace('/\x1B\[([0-9]{1,2}(;[0-9]{1,2})?)?[m|K]/', '', $commandTester->getDisplay());
        $this->assertTrue(stripos($display, 'Sanity Command Utility 1.0') !== false, $display);
    }
}
