<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Configuration;

use Blend\Component\Configuration\Configuration;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class TestConfiguration extends Configuration {
    // test class
}

/**
 * Test class for Filesystem
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase {

    protected $fixturesFolder;

    protected function setUp() {
        parent::setUp();
        $this->fixturesFolder = dirname(__FILE__) . '/fixtures';
    }

    public function testLoaderDumper() {
        $conf = new TestConfiguration([
            'key1' => 'value1',
            'key2' => 2,
            'key3' => [1, 2, 3, 4]
        ]);
        $dumpFile = sys_get_temp_dir() . '/' . uniqid() . '.cache';
        $conf->dump($dumpFile);
        $this->assertFileExists($dumpFile);

        $conf2 = new TestConfiguration();
        $conf2->load($dumpFile);
        $this->assertEquals('value1', $conf->get('key1'));
        $this->assertEquals(2, $conf->get('key2'));
        $this->assertEquals([1, 2, 3, 4], $conf->get('key3'));
        unlink($dumpFile);
    }

    public function testConfigSanity() {
        $conf = new TestConfiguration();
        $this->assertTrue($conf instanceof Configuration);
    }

    public function testLevel1() {
        $conf = new TestConfiguration([
            'key1' => 'value1',
            'key2' => 2,
            'key3' => [1, 2, 3, 4]
        ]);
        $this->assertEquals('value1', $conf->get('key1'));
        $this->assertEquals(2, $conf->get('key2'));
        $this->assertEquals([1, 2, 3, 4], $conf->get('key3'));
    }

    public function testLevel2() {
        $conf = new TestConfiguration([
            'log' => [
                'level' => 'DEBUG',
                'file' => 'debug.file'
            ],
            'database' => [
                'username' => 'dbuser',
                'database' => 'testdb'
            ],
            'translation' => [
                'languages' => ['en', 'nl', 'cz']
            ]
        ]);
        $this->assertEquals('DEBUG', $conf->get('log.level'));
        $this->assertEquals('debug.file', $conf->get('log.file'));
        $this->assertEquals('dbuser', $conf->get('database.username'));
        $this->assertEquals(['en', 'nl', 'cz'], $conf->get('translation.languages'));
    }

    public function testMultiLevel() {
        $conf = new TestConfiguration([
            'a' => [
                'b' => [
                    'c' => [
                        'd' => [1, 2, 3]
                    ]
                ]
            ],
            'x' => [
                'y' => [
                    'z' => [
                        'm' => false
                    ]
                ]
            ]
        ]);
        $this->assertFalse($conf->get('x.y.z.m'));
    }

    public function testMerge() {
        $c = new TestConfiguration([
            'db' => [
                'user' => 'app',
                'passwd' => 'test'
            ],
        ]);
        $c->mergeWith([
            'db' => [
                'passwd' => '123',
                'host' => 'local'
            ]
        ]);
        $this->assertEquals('app', $c->get('db.user'));
        $this->assertEquals('123', $c->get('db.passwd'));
        $this->assertEquals('local', $c->get('db.host'));
    }

    public function testTestFileTest1() {
        $conf = Configuration::createFromFile($this->fixturesFolder . '/test1.json');
        $this->assertTrue($conf->has('section1.stringValue'));
        $this->assertTrue($conf->has('section1.numberValue'));
        $this->assertTrue($conf->has('section1.arrayValue'));
        $arrayValue = $conf->get('section1.arrayValue');
        $this->assertCount(3, $arrayValue);
    }

    public function testTestFileAppWithEnv() {
        $conf = Configuration::createFromFile($this->fixturesFolder . '/app.json');
        $this->assertEquals('app_production', $conf->get('database.database'));
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\FileNotFoundException
     */
    public function testTestFileMissing() {
        $conf = Configuration::createFromFile($this->fixturesFolder . '/aaaa.json');
    }

}
