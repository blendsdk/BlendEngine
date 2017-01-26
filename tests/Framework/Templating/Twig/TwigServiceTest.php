<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\tests\Framework\Templating\Twig;

use Blend\Component\DI\ServiceContainer;
use Blend\Component\Templating\TemplateEngineInterface;
use Blend\Component\Templating\Twig\TwigEngine;
use Blend\Framework\Factory\ApplicationFactory;
use Blend\Framework\Factory\TranslatorFactory;
use Blend\Framework\Factory\TwigEngineFactory;
use Blend\Framework\Locale\LocaleService;
use Blend\Framework\Templating\Twig\TwigEngineService;
use Blend\Tests\Framework\Templating\Twig\Stubs\TwigModule;
use Blend\Tests\Framework\Translation\Stubs\TestTranslationProvider;
use Blend\Tests\ProjectUtil;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of TwigServiceTest.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigServiceTest extends \PHPUnit_Framework_TestCase
{
    public static $cleanup = array();

    /**
     * @var string
     */
    public static $templateRoot;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$templateRoot = __DIR__ . '/templates';
    }

    /**
     * @return TwigEngineService
     */
    private function engine(array $params = array())
    {
        $container = new ServiceContainer();

        $container->setScalars(array(
            '_app_cache_folder' => TEMP_DIR,
            '_debug' => true,
        ));

        return $container->get(TwigEngineFactory::class)
                        ->setViewPaths(array(__DIR__ . '/templates'));
    }

    public function testEngineSanity()
    {
        $result = $this->engine()->render('hello.twig', array('name' => 'World'));
        $this->assertEquals('Hello World!', $result);
    }

    public function testEuroCurrency()
    {
        $result = $this->engine()->render('euro.twig', array('value' => 100, TwigEngine::TRIM_RESULT => true));
        $this->assertEquals('&euro;100,-', $result);
    }

    public function testRoutingExtension()
    {
        $appName = 'TwigRouting';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        $loader->addPsr4('Acme' . '\\', $projectFolder . '/src/Acme');
        $factory = new ApplicationFactory($clazz, $projectFolder, true);
        /* @var $app TestableApplication */
        $app = $factory->create();
        $app->loadServices(array(
            'runtime' => 'TwigRouting\\TwigRoutingRuntime',
            'twig-module' => TwigModule::class,
            'locale-service' => LocaleService::class,
            TranslatorInterface::class => TranslatorFactory::class,
            'test-translations' => TestTranslationProvider::class,
            TemplateEngineInterface::class => TwigEngineFactory::class,
        ));
        $app->reInstallEventSubscribers();
        $request = Request::create('/urltest/am');
        ProjectUtil::addSession($request);
        $output = catch_output(function () use ($app, $request) {
            $app->run($request);
        });
        $this->assertEquals('http://localhost/urltest/am?hello=world', $output);
        self::$cleanup[] = $projectFolder;
    }
}
