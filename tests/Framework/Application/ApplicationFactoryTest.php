<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Application;

use Blend\Tests\ProjectUtil;
use Blend\Component\Filesystem\Filesystem;
use Blend\Framework\Application\ApplicationFactory;
use Blend\Tests\Framework\Application\Stubs\DummyApplication;

/**
 * Description of ApplicationFactoryTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ApplicationFactoryTest extends \PHPUnit_Framework_TestCase {

    static $cleanup = [];

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\FileNotFoundException
     */
    public function testNoCacheFolder() {
        $factory = new ApplicationFactory();
        $factory->create(DummyApplication::class, '/', 'dummy');
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\FileNotFoundException
     */
    public function testWithNoConfigFile() {
        $factory = new ApplicationFactory();
        $fs = new Filesystem();
        $appdir = sys_get_temp_dir() . '/' . uniqid();
        $fs->mkdir($appdir . '/var/cache');
        self::$cleanup[] = $appdir;
        $factory->create(DummyApplication::class, $appdir);
    }

    public function testFactorySanity() {
        $appName = 'App1';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        $factory = new ApplicationFactory();
        $factory->create($clazz, $projectFolder, true);
        $configCache = $projectFolder . '/var/cache/config.cache';
        $this->assertFileExists($projectFolder . '/var/log/application-' . date('Y-m-d') . '.log');
        $this->assertFileExists($configCache);

        unlink($projectFolder . '/config/config.json');

        $app2 = $factory->create($clazz, $projectFolder);
        $this->assertTrue($app2 instanceof \Blend\Framework\Application\Application);

        $loader->unregister();
    }

    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
        $fs = new Filesystem();
        foreach (self::$cleanup as $folder) {
            $fs->remove($folder);
        }
    }

}
