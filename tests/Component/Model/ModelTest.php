<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Model;

use Blend\Component\Model\Model;

/**
 * ModelTest.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testSanity()
    {
        $m = new Model();

        $this->assertTrue($m->isNew());

        $m2 = new Model(array('firstname' => 'Johny', 'lastname' => 'Bravo', 'age' => 40, 'salary' => 500));
        $this->assertFalse($m2->isNew());

        $m2->setValue('firstname', 'Peter');
        $this->assertFalse($m2->isNew());

        $this->assertEquals('Peter', $m2->getValue('firstname'));
        $this->assertEquals('Bravo', $m2->getValue('lastname'));

        $updates = array_keys($m2->getUpdates());
        $diff = array_diff(array('firstname'), $updates);
        $this->assertCount(0, $diff);

        $m2->setValue('age', 42);
        $updates = array_keys($m2->getUpdates());
        $diff = array_diff(array('firstname', 'age'), $updates);
        $this->assertCount(0, $diff);

        $data = array_values($m2->getData());
        $diff = array_diff(array('Peter', 'Bravo', 42, 500), $data);
        $this->assertCount(0, $diff);

        $initial = array_values($m2->getInitial());
        $diff = array_diff(array('Johny', 'Bravo', 40, 500), $initial);
        $this->assertCount(0, $diff);
    }
}
