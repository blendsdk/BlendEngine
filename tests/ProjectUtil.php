<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests;

use Blend\Component\DI\Container;
use Blend\Component\Filesystem\Filesystem;
use Blend\ProjectSetup\SetupApplication;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

/**
 * ProjectUtil is a utility class for creating sub-project and run console
 * command in the test suite.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ProjectUtil
{
    /**
     * Runa a command in and returns the commandTester object. This method can
     * run either a blend command or a sub-application command.
     *
     * @param string $projectFolder
     * @param string $commandName
     * @param string $params
     * @param string $app           In case of null it will be set to blend. In case of
     *                              className as string, the application's class name will be used
     * @param mixed  $runOptions
     *
     * @return CommandTester
     */
    public static function runCommand($projectFolder, $commandName, array $params = array(), $app = null, $runOptions = array())
    {
        $loader = new ClassLoader();
        $curDir = getcwd();
        chdir($projectFolder);
        if ($app === null) {
            $app = new SetupApplication($projectFolder);
        } elseif (is_string($app)) {
            $classes = explode('\\', $app);
            $loader->addPsr4("{$classes[0]}\\", $projectFolder . '/src/');
            $loader->register(true);

            $c = new Container();
            $c->defineSingletonWithInterface('app', $app, array('scriptPath' => $projectFolder . '/bin'));
            $app = $c->get('app');
        }
        $commandTester = new CommandTester($app->find($commandName));
        $commandTester->execute($params, $runOptions);
        chdir($curDir);
        $c = null;
        if ($loader) {
            $loader->unregister();
        }

        return $commandTester;
    }

    /**
     * Create a new sub project.
     *
     * @param string $projectName
     * @param bool   $rebuild
     *
     * @return string
     */
    public static function createNewProject($projectName, $rebuild = false)
    {
        $fs = new Filesystem();
        $projectFolder = TEMP_DIR . '/TestProjects/' . $projectName;
        if ($rebuild) {
            if ($fs->exists($projectFolder)) {
                $fs->remove($projectFolder);
            }
        }

        $fs->ensureFolder($projectFolder);
        $projectFolder = realpath($projectFolder);
        self::runCommand($projectFolder, 'init', array('--testable' => true));

        return $projectFolder;
    }

    /**
     * Creates and registers an ClassLoder for a given project.
     *
     * @param type       $projectFolder
     * @param null|mixed $ns
     *
     * @return type
     */
    public static function initProjectClassLoader($projectFolder, $ns = null)
    {
        if ($ns === null) {
            $path = explode(DIRECTORY_SEPARATOR, $projectFolder);
            $ns = end($path);
        }
        $loader = new ClassLoader();
        $loader->addPsr4($ns . '\\', $projectFolder . '/src/');
        $loader->register(true);

        return array("{$ns}\\{$ns}Application", $loader);
    }

    public static function addSession(Request $request)
    {
        $request->setSession(new Session(new MockFileSessionStorage(TEMP_DIR)));
    }
}
