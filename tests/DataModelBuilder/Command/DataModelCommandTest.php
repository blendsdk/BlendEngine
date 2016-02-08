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

/**
 * DataModelCommandTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DataModelCommandTest extends DatabaseTestBase {

    private static $projectFolder;

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSanityCommandTest() {
        $app = $this->createApplication();
        ProjectUtil::runCommand(self::$projectFolder, 'datamodel:generate', ['--config' => 'not-exist.php'], $app);
    }

    public function testDefaultParameters() {
        $app = $this->createApplication();
        ProjectUtil::runCommand(self::$projectFolder, 'datamodel:generate', [], $app, ['verbosity' => true]);
    }

    public static function getTestingDatabaseConfig() {
        return [
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => __CLASS__
        ];
    }

    private function createApplication() {
        $app = new Application(self::$projectFolder, 'DAL');
        $app->add(new DataModelCommand());
        return $app;
    }

    public static function setUpSchema() {
        self::$currentDatabase->executeScript(file_get_contents(__DIR__ . '/scripts/schema.sql'));
        $projectFolder = ProjectUtil::createNewProject("DALTest", true);
        self::$projectFolder = $projectFolder;
    }

}
