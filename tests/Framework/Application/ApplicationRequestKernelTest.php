<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Application;

use Blend\Component\Filesystem\Filesystem;
use Blend\Framework\Factory\ApplicationFactory;
use Blend\Tests\Framework\Application\Stubs\ControllerTestModule;
use Blend\Tests\Framework\Application\Stubs\CustomRequestExceptionHandler;
use Blend\Tests\ProjectUtil;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ApplicationRequestKernelTest extends \PHPUnit_Framework_TestCase
{
    public static $cleanup = array();

    /**
     * @large
     */
    public function testNoControllerKeyPareError()
    {
        $appName = 'App15';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        $factory = new ApplicationFactory($clazz, $projectFolder);
        $app = $factory->create();
        $app->loadServices(array(
            'custom-exception-handler' => CustomRequestExceptionHandler::class,
            'controller-test-module' => ControllerTestModule::class,
        ));
        $app->reInstallEventSubscribers();
        $request = Request::create('/no-response');
        $output = catch_output(function () use ($app, $request) {
            $app->run($request);
        });
        $this->assertEquals('Server error', $output);
        self::$cleanup[] = $projectFolder;
    }

    /**
     * @large
     */
    public function testControllerHandlerWithActionParameters()
    {
        $appName = 'App16';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        $factory = new ApplicationFactory($clazz, $projectFolder);
        $app = $factory->create();
        $app->loadServices(array(
            'custom-exception-handler' => CustomRequestExceptionHandler::class,
            'controller-test-module' => ControllerTestModule::class,
        ));
        $app->reInstallEventSubscribers();
        $output = catch_output(function () use ($app) {
            $app->run(Request::create('/ping'));
        });
        $this->assertContains('pong', $output);

        $factory = new ApplicationFactory($clazz, $projectFolder);
        $app = $factory->create();
        $output = catch_output(function () use ($app) {
            $request = Request::create('/hello/Johny/Bravo');
            $app->run($request);
        });
        $this->assertContains('Hello Johny Bravo from /hello/Johny/Bravo', $output);
        self::$cleanup[] = $projectFolder;
    }

    public function testJSONResponse()
    {
        $appName = 'App15';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        $factory = new ApplicationFactory($clazz, $projectFolder);
        $app = $factory->create();
        $app->loadServices(array(
            'custom-exception-handler' => CustomRequestExceptionHandler::class,
            'controller-test-module' => ControllerTestModule::class,
        ));
        $app->reInstallEventSubscribers();
        $output = catch_output(function () use ($app) {
            $request = Request::create('/api/hello/world');
            $app->run($request);
        });
        $this->assertEquals('{"hello":"world"}', $output);
        self::$cleanup[] = $projectFolder;
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $fs = new Filesystem();
        foreach (self::$cleanup as $folder) {
            $fs->remove($folder);
        }
    }
}
