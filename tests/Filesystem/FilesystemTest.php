<?php

namespace Blend\Tests\Filesystem;

use Blend\Component\Filesystem\Filesystem;

/**
 * Test class for Filesystem
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class FilesystemTests extends \PHPUnit_Framework_TestCase {

    protected $cleanup = array();

    public function testEnsureFolder() {
        $fs = new Filesystem();
        $thisFolder = dirname(__FILE__);
        $folder = $fs->ensureFolder($thisFolder . DIRECTORY_SEPARATOR . __FUNCTION__);
        $this->assertFileExists($folder);
        $this->cleanup[] = $folder;
    }

    protected function tearDown() {
        parent::tearDown();
        $fs = new Filesystem();
        $fs->remove($this->cleanup);
    }

}
