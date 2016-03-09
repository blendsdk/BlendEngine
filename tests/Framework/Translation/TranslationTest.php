<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Translation;

use Blend\Tests\ProjectUtil;
use Blend\Framework\Factory\TranslatorFactory;
use Blend\Framework\Locale\LocaleService;
use Blend\Framework\Factory\ApplicationFactory;
use Blend\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Blend\Tests\Framework\Translation\Stubs\TestableApplication;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Description of TranslationTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TranslationTest extends \PHPUnit_Framework_TestCase {

    static $cleanup = [];

    public function testTranslationFactory() {
        $appName = 'TransApp1';
        $projectFolder = ProjectUtil::createNewProject($appName, true);
        list($clazz, $loader) = ProjectUtil::initProjectClassLoader($projectFolder);
        ProjectUtil::appendOrCreateServicesConfig($projectFolder, [
            'acme-module' => [$projectFolder . '/src/Acme', 'Acme\\Acme'],
            'locale-service' => LocaleService::class,
            TranslatorInterface::class => TranslatorFactory::class,
            'test-translations' => Stubs\TestTranslationProvider::class
        ]);
        $factory = new ApplicationFactory(TestableApplication::class, $projectFolder);
        /* @var $app TestableApplication */
        $app = $factory->create();
        $request = Request::create("/am");
        ProjectUtil::addSession($request);
        $output = catch_output(function() use($app, $request) {
            $app->run($request);
        });
        $translator = $app->getContainer()->get(TranslatorInterface::class);

        $this->assertEquals('Bari louys Blend!', $translator->trans('Good morning :name!', [
                    ':name' => 'Blend'
        ]));
        //self::$cleanup[] = $projectFolder;
    }

    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
        $fs = new Filesystem();
        foreach (self::$cleanup as $folder) {
            $fs->remove($folder);
        }
    }

}
