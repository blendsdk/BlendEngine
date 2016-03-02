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
use Blend\Framework\Factory\ApplicationFactory;
use Blend\Tests\Framework\Application\Stubs\DummyApplication;
use Symfony\Component\HttpFoundation\Request;
use Blend\Tests\Framework\Application\Stubs\ControllerTestModule;
use Blend\Tests\Framework\Application\Stubs\TestableApplication;
use Blend\Tests\Framework\Application\Stubs\CustomRequestExceptionHandler;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ApplicationRequestKernelTest extends \PHPUnit_Framework_TestCase {

    static $cleanup = [];

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testNoControllerKeyPare() {
        $appName = 'App15';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        ProjectUtil::appendOrCreateServicesConfig($projectFolder, [
            'custom-exception-handler' => CustomRequestExceptionHandler::class,
            'controller-test-module' => ControllerTestModule::class
        ]);
        $factory = new ApplicationFactory(TestableApplication::class, $projectFolder);
        $app = $factory->create();
        $request = Request::create("/no-response");
        $output = catch_output(function() use($app, $request) {
            $app->run($request);
        });
        self::$cleanup[] = $projectFolder;
    }

    public function testControllerKeyPare() {
        $appName = 'App16';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        ProjectUtil::appendOrCreateServicesConfig($projectFolder, [
            'custom-exception-handler' => CustomRequestExceptionHandler::class,
            'controller-test-module' => ControllerTestModule::class
        ]);
        $factory = new ApplicationFactory($clazz, $projectFolder);
        $app = $factory->create();
        $output = catch_output(function() use($app) {
            $app->run(Request::create("/ping"));
        });
        $this->assertEquals($output, 'pong');

        $factory = new ApplicationFactory($clazz, $projectFolder);
        $app = $factory->create();
        $output = catch_output(function() use($app) {
            $request = Request::create("/hello/Johny/Bravo");
            $app->run($request);
        });
        $this->assertEquals($output, 'Hello Johny Bravo from /hello/Johny/Bravo');
        self::$cleanup[] = $projectFolder;
    }

    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
        $fs = new Filesystem();
        foreach (self::$cleanup as $folder) {
            $fs->remove($folder);
        }
    }

}
