<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\tests\Framework\Templating\Twig;

use Blend\Framework\Factory\TwigEngineFactory;
use Blend\Framework\Templating\Twig\TwigEngineService;
use Blend\Component\Templating\EngineInterface;
use Blend\Component\DI\ServiceContainer;
use Blend\Tests\Framework\Translation\Stubs\TestTranslationProvider;
use Blend\Tests\ProjectUtil;
use Blend\Framework\Factory\ApplicationFactory;
use Blend\Framework\Locale\LocaleService;
use Blend\Tests\Framework\Templating\Twig\Stubs\TwigModule;
use Blend\Tests\Framework\Templating\Twig\Stubs\TwigController;
use Blend\Framework\Factory\TranslatorFactory;
use Symfony\Component\HttpFoundation\Request;
use Blend\Framework\Support\Runtime\Runtime;
use Blend\Framework\Support\Runtime\RuntimeProviderInterface;

class DummyRuntime extends Runtime {

}

/**
 * Description of TwigServiceTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigServiceTest extends \PHPUnit_Framework_TestCase {

    static $cleanup = [];

    /**
     * @var string
     */
    static $templateRoot;

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        self::$templateRoot = __DIR__ . '/templates';        
    }

    /**
     * @return TwigEngineService
     */
    private function engine(array $params = []) {

        $container = new ServiceContainer();        

//TODO: reformat
        $container->defineSingletonWithInterface(RuntimeProviderInterface::class,DummyRuntime::class);
        $container->setScalars([
            '_app_cache_folder' => TEMP_DIR,
            '_debug' => true            
        ]);

        return $container->get(TwigEngineFactory::class);
    }

    public function testEngineSanity() {
        $result = $this->engine()->render('hello.twig', ['name' => 'World']);
        $this->assertEquals('Hello World!', $result);
    }

    public function _testEuroCurrency() {
        $result = $this->engine()->render('euro.twig', ['value' => 100, '_trim' => true]);
        $this->assertEquals('&euro;100,-', $result);
    }

    public function _testRoutingExtension() {
        $appName = 'TwigRouting';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        $loader->addPsr4("Acme" . '\\', $projectFolder .'/src/Acme');
        $factory = new ApplicationFactory($clazz, $projectFolder, true);
        /* @var $app TestableApplication */
        $app = $factory->create();
        $app->loadServices([
            "runtime" => 'TwigRouting\\TwigRoutingRuntime',
            "twig-module" => TwigModule::class,
            'locale-service' => LocaleService::class,
            TranslatorInterface::class => TranslatorFactory::class,
            'test-translations' => TestTranslationProvider::class,
            EngineInterface::class => TwigEngineService::class,
        ]);
        $app->reInstallEventSubscribers();
        $request = Request::create("/urltest/am");
        ProjectUtil::addSession($request);
        $output = catch_output(function() use($app, $request) {
            $app->run($request);
        });
        $this->assertEquals("http://localhost/urltest/am?hello=world", $output);
        self::$cleanup[] = $projectFolder;
    }

}
