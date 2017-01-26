<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Filesystem;

use Blend\Component\Filesystem\Filesystem;

/**
 * Test class for Filesystem.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class FilesystemTests extends \PHPUnit_Framework_TestCase
{
    protected $cleanup = array();

    /**
     * @expectedException \Exception
     */
    public function testAssertWritableException()
    {
        $fs = new Filesystem();
        if (!is_windows()) {
            $fs->assertFolderWritable('/root');
        } else {
            //skip this test on Windows
            throw new \Exception('');
        }
    }

    public function testAssertWritable()
    {
        $fs = new Filesystem();
        if (!is_windows()) {
            $this->assertEquals('/tmp', $fs->assertFolderWritable('/tmp'));
        } else {
            $this->assertTrue(true);
        }
    }

    public function testEnsureFolder()
    {
        $fs = new Filesystem();
        $thisFolder = dirname(__FILE__);
        $folder = $fs->ensureFolder($thisFolder . DIRECTORY_SEPARATOR . __FUNCTION__);
        $this->assertFileExists($folder);
        $this->cleanup[] = $folder;
    }

    protected function tearDown()
    {
        parent::tearDown();
        $fs = new Filesystem();
        $fs->remove($this->cleanup);
    }
}
