<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\DataModelBuilder\Command;

use Blend\Component\Console\Application;
use Blend\DataModelBuilder\Command\DataModelCommand;
use Blend\Tests\Component\Database\DatabaseTestBase;
use Blend\Tests\ProjectUtil;
use Blend\Component\DI\Container;
use Blend\Component\Filesystem\Filesystem;
use Composer\Autoload\ClassLoader;

/**
 * DataModelCommandTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DataModelCommandTest extends DatabaseTestBase {

    private static $projectFolder;

    public function testDefaultConfig() {
        $app = $this->createApplication();
        ProjectUtil::runCommand(self::$projectFolder, 'datamodel:generate', [], $app);
        $this->assertFileExists(self::$projectFolder . '/src/Database/Common/Model/SysUser.php');
        $this->assertFileExists(self::$projectFolder . '/src/Database/Common/Model/SysUserProfile.php');
    }

    public function testCustomizedConfig() {
        $app = $this->createApplication();
        ProjectUtil::runCommand(self::$projectFolder, 'datamodel:generate', ['--configclass' => 'Blend\Tests\DataModelBuilder\Command\CustomizedModelConfig'], $app);
        $this->assertFileExists(self::$projectFolder . '/src/Database/Common/Model/SysOrder.php');
        $this->assertFileExists(self::$projectFolder . '/src/Database/Common/Model/Base/SysOrder.php');


        $loader = new ClassLoader();
        $loader->addPsr4("DALTest\\", self::$projectFolder . '/src/');
        $loader->register();

        $c = new Container();
        $f = $c->get('DALTest\Database\Common\Factory\SysUserFactory', [
            'database' => self::$currentDatabase
        ]);
        $f->createNewModel(['user_name' => 'Gevik', 'date_created' => '2016-02-09 22:13:55']);
    }

    public static function getTestingDatabaseConfig() {
        return [
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => 'daltest'
        ];
    }

    private function createApplication() {
        $app = new Application(self::$projectFolder, 'DALTest');
        $app->add(new DataModelCommand());
        return $app;
    }

    protected function setUp() {
        if (is_windows()) {
            $fs = new Filesystem();
            $fs->remove(self::$projectFolder . '/src/Database');
        }
    }

    public static function setUpSchema() {
        define('BLEND_APPLICATION_NAMESPACE', 'DALTest');
        self::$currentDatabase->executeScript(file_get_contents(__DIR__ . '/scripts/schema.sql'));
        $projectFolder = ProjectUtil::createNewProject("DALTest", !is_windows());
        self::$projectFolder = $projectFolder;
    }

}
