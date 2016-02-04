<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blend\Tests\ProjectSetup;

use Blend\Tests\ProjectUtil;

/**
 * Description of SetupApplicationTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SetupApplicationTest extends \PHPUnit_Framework_TestCase {

    public function testSanity() {
        $projectFolder = ProjectUtil::createNewProject('sanity', true);
        $this->assertTrue(file_exists("$projectFolder/bin/sanity.php"));
        $this->assertTrue(file_exists("$projectFolder/web/css/sanity.css"));
        $commandTester = ProjectUtil::runCommand($projectFolder, 'list', [], 'Sanity\Console\SanityApplication');
        $this->assertTrue(stripos($commandTester->getDisplay(), "Sanity Command Utility version 1.0") !== false);
    }

}
