<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Application;

use Symfony\Component\HttpFoundation\Request;

/**
 * ApplicationTest.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidResponse()
    {
        $app = new Stubs\SanityApp();
        $result = catch_output(function () use ($app) {
            return $app->run(Request::create('http://example.com'));
        });
        $this->assertContains('handleRequest', $result);
    }
}
