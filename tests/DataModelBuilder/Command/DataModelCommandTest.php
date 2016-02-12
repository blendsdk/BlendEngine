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

    public function testFactory() {
        $app = $this->createApplication();
        ProjectUtil::runCommand(self::$projectFolder, 'datamodel:generate', ['--configclass' => 'Blend\Tests\DataModelBuilder\Command\CustomizedModelConfig'], $app);
        $loader = new ClassLoader();
        $loader->addPsr4("DALTest\\", self::$projectFolder . '/src/');
        $loader->register();

        $this->assertFileExists(self::$projectFolder . '/src/Database/Common/Model/SysOrder.php');
        $this->assertFileExists(self::$projectFolder . '/src/Database/Common/Model/Base/SysOrder.php');
        $this->assertFileExists(self::$projectFolder . '/src/Database/Common/Factory/SysOrderFactory.php');
        $this->assertFileExists(self::$projectFolder . '/src/Database/Common/Factory/Base/SysOrderFactory.php');

        $c = new Container();

        /* @var $userFactory \DALTest\Database\Common\Factory\SysUserFactory */
        $userFactory = $c->get('DALTest\Database\Common\Factory\SysUserFactory', ['database' => self::$currentDatabase]);

        /* @var $user \DALTest\Database\Common\Model\SysUser */
        $user = $userFactory->newModel();
        $user->setUserEmail('JOHNY@DOE.COM');
        $user->setUserPassword('test123');
        $user->setUserName('Johny');
        $userFactory->saveObject($user);

        $this->assertEquals('johny@doe.com', $user->getUserEmail());
        $this->assertEquals(sha1('test123'), $user->getUserPassword());

        $user->setUserEmail('johny@bravo.com');
        $user->setNullableColumn('it is not null now');
        $userFactory->saveObject($user);
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
