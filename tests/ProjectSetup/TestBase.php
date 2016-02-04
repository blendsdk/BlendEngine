<?php

namespace Blend\Tests\ProjectSetup;

use Blend\Component\Filesystem\Filesystem;
use Blend\ProjectSetup\SetupApplication;
use Symfony\Component\Console\Tester\CommandTester;
use Composer\Autoload\ClassLoader;
use Blend\Component\DI\Container;

/**
 * Description of TestBase
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TestBase extends \PHPUnit_Framework_TestCase {

    /**
     * Runs a command and retuns the CommandTester
     * @param string $projectFolder
     * @param string $commandName
     * @param array $params
     * @return CommandTester
     */
    protected static function runCommand($projectFolder, $commandName, array $params = [], $app = null) {
        $curDir = getcwd();
        chdir($projectFolder);
        if ($app === null) {
            $app = new SetupApplication($projectFolder);
        } else if (is_string($app)) {

            $classes = explode('\\', $app);
            $loader = new ClassLoader();
            $loader->addPsr4("{$classes[0]}\\", $projectFolder . '/src/');
            $loader->register(true);

            $c = new Container();
            $c->define('app', [
                'class' => $app,
                'scriptPath' => $projectFolder . '/bin'
            ]);

            $app = $c->get('app');
        }
        $commandTester = new CommandTester($app->find($commandName));
        $commandTester->execute($params);
        chdir($curDir);
        return $commandTester;
    }

    /**
     * Create a new sub project
     * @param string $projectName
     * @param boolean $rebuild
     * @return string
     */
    protected static function createNewProject($projectName, $rebuild = false) {

        $fs = new Filesystem();
        $projectFolder = dirname(__FILE__) . '/../TestProjects/' . $projectName;
        if ($rebuild) {
            if ($fs->exists($projectFolder)) {
                $fs->remove($projectFolder);
            }
        }

        $fs->ensureFolder($projectFolder);
        $projectFolder = realpath($projectFolder);
        self::runCommand($projectFolder, 'project:init');
        return $projectFolder;
    }

}
