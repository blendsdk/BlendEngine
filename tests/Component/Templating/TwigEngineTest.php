<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Templating;

use Blend\Component\Templating\Twig\TwigEngine;

/**
 * TwigEngineTest.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigEngineTest extends \PHPUnit_Framework_TestCase
{
    public function testTwigEngine()
    {
        $engine = new TwigEngine(__DIR__ . '/templates', TEMP_DIR, true);
        $result = $engine->render('hello.twig', array(
            'name' => 'Cate',
        ));
        $this->assertEquals('Hello Cate!', $result);
    }
}
