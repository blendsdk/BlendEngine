<?php

namespace Blend\Tests;

use Blend\Component\Configiration;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Test class for Filesystem
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase {

    protected $fixturesFolder;

    protected function setUp() {
        parent::setUp();
        $this->fixturesFolder = dirname(__FILE__) . '/fixtures/configuration';
    }

    public function testTestFileTest1() {
        $conf = new Configiration($this->fixturesFolder . '/test1.php');
        $this->assertTrue($conf->has('section1.stringValue'));
        $this->assertTrue($conf->has('section1.numberValue'));
        $this->assertTrue($conf->has('section1.arrayValue'));

        $arrayValue = $conf->get('section1.arrayValue');
        $this->assertCount(3, $arrayValue);
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\FileNotFoundException
     */
    public function testTestFileMissing() {
        $conf = new Configiration($this->fixturesFolder . '/aaaa.php');
    }

}
