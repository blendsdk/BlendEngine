<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Console\Command;

use Blend\Framework\Console\Application;
use Blend\Tests\ProjectUtil;

/**
 * Description of CommandTest.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testSanityCommandTest()
    {
        $projectFolder = ProjectUtil::createNewProject('CommandTest', true);
        $app = new Application($projectFolder, 'CommandTest');
        $app->add(new TestCommand());
        $result = ProjectUtil::runCommand($projectFolder, 'test:testcommand', array(), $app);
    }
}
