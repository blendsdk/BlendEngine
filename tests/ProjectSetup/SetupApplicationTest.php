<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blend\Tests\ProjectSetup;

use Blend\Tests\ProjectSetup\TestBase;

/**
 * Description of SetupApplicationTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SetupApplicationTest extends TestBase {

    public function testSanity() {
        $projectFolder = self::createNewProject('sanity', true);
        $this->assertTrue(file_exists("$projectFolder/bin/sanity.php"));
        $this->assertTrue(file_exists("$projectFolder/web/css/sanity.css"));
    }

}
