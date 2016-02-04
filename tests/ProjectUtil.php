<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests;

use Blend\Component\Filesystem\Filesystem;
use Blend\ProjectSetup\SetupApplication;
use Symfony\Component\Console\Tester\CommandTester;
use Composer\Autoload\ClassLoader;
use Blend\Component\DI\Container;

/**
 * ProjectUtil is a utility class for creating sub-project and run console
 * command in the test suite
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ProjectUtil {

    /**
     * Runa a command in and returns the commandTester object. This method can
     * run either a blend command or a sub-application command
     * @param string $projectFolder
     * @param string $commandName
     * @param string $params
     * @param string $app In case of null it will be set to blend. In case of
     * className as string, the application's class name will be used
     * @return CommandTester
     */
    public static function runCommand($projectFolder, $commandName, array $params = [], $app = null) {
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
    public static function createNewProject($projectName, $rebuild = false) {

        $fs = new Filesystem();
        $projectFolder = dirname(__FILE__) . '/TestProjects/' . $projectName;
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
