<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Form;

use Blend\Tests\Component\Form\Stubs\TestForm;
use Blend\Tests\ProjectUtil;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRequest()
    {
        $request = Request::create('/?name=hello');
        ProjectUtil::addSession($request);
        $form = new TestForm($request);
        $this->assertEquals('hello', $form->process());
    }

    public function testGetInvalidRequest()
    {
        $request = Request::create('/?error=hello');
        ProjectUtil::addSession($request);
        $form = new TestForm($request);
        $this->assertFalse($form->process());
    }

    public function testPostRequest()
    {
        $request = Request::create('/', 'POST');
        ProjectUtil::addSession($request);
        $form = new TestForm($request);
        $form->submit();
        $result = $form->process();
        $this->assertEquals('no salary', $result['error'][0][0]);
    }
}
