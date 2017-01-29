<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Support;

use Blend\Component\Filesystem\Filesystem;
use Blend\Framework\Factory\ApplicationFactory;
use Blend\Tests\Framework\Application\Stubs\ControllerTestModule;
use Blend\Tests\ProjectUtil;
use Symfony\Component\HttpFoundation\Request;

class TrailingSlashRedirectServiceTest extends \PHPUnit_Framework_TestCase
{
    public static $cleanup = array();

    public function testRedirect()
    {
        $appName = 'App150';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        $loader->addPsr4('Acme' . '\\', $projectFolder .'/src/Acme');
        $factory = new ApplicationFactory($clazz, $projectFolder);
        $app = $factory->create();
        $app->loadServices(array(
            'controller-test-module' => ControllerTestModule::class,
        ));
        $app->reInstallEventSubscribers();
        $request = Request::create('/ping/');
        $output = catch_output(function () use ($app, $request) {
            $app->run($request);
        });
        $this->assertContains('Redirecting to /ping', $output);
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
