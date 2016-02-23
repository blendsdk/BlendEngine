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
use Composer\Autoload\ClassLoader;
use Blend\Component\DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of ApplicationTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ClassLoader 
     */
    protected $currentLoader;

    /**
     * @var Container; 
     */
    protected $container;

    public function autoLoadProject($projectFolder) {
        if ($this->currentLoader) {
            $this->currentLoader->unregister();
            $this->currentLoader = null;
        }
        $path = explode(DIRECTORY_SEPARATOR, $projectFolder);
        $ns = end($path);
        $loader = new ClassLoader();
        $loader->addPsr4($ns . '\\', $projectFolder . '/src/');
        $loader->register(true);
        $this->currentLoader = $loader;
        $this->container = new Container();
        return "{$ns}\\{$ns}Application";
    }

    public function testLoadConfig() {
        $appName = 'LoadConfig';
        $projectFolder = ProjectUtil::createNewProject($appName, true);

        $app = new Stubs\LoadConfigStubApplication($projectFolder);
        $app->run(Request::create('/'));
        $this->assertEquals('5432', $app->testGetConfigValue('database.port'));
        $this->assertFileExists("{$projectFolder}/var/cache/config.cache");

        file_put_contents("{$projectFolder}/config/config.php", '');
        $app2 = new Stubs\LoadConfigStubApplication($projectFolder);
        $app2->run(Request::create('/'));
        $this->assertEquals('5432', $app2->testGetConfigValue('database.port'));
    }

    public function testLogger() {
        $appName = 'LoggerConfig';
        $projectFolder = ProjectUtil::createNewProject($appName, true);

        $app = new Stubs\LoggerStubApplication($projectFolder);
        $app->run(Request::create('/'));
        $app->testLog('Hello World');
        $this->assertFileExists("{$projectFolder}/var/log/loggerapp-" . date('Y-m-d') . ".log");
    }

}
