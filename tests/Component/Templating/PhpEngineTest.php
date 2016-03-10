<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Templating;

use Blend\Component\Templating\Php\PhpEngine;

/**
 * Description of PhpEngineTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class PhpEngineTest extends \PHPUnit_Framework_TestCase {

    public function testPhpEngine() {
        $engine = new PhpEngine();
        $result = $engine->render(__DIR__ . '/templates/hello.php', [
            'name' => 'Johny'
        ]);
        $this->assertEquals('Hello Johny', $result);
    }

    public function testPhpEngineWithExtension() {
        $engine = new PhpEngine(true, [
            'h1' => function($val) {
                return "<h1>{$val}</h1>";
            }
        ]);
        $result = $engine->render(__DIR__ . '/templates/hello_ex.php', [
            'name' => 'Johny'
        ]);
        $this->assertEquals('<h1>Hello Johny</h1>', $result);
    }

}
