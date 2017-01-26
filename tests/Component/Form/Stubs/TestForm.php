<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Form\Stubs;

use Blend\Component\Form\Form;

/**
 * Description of TestForm.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TestForm extends Form
{
    private $emulateSubmit;

    const FIELD_NAME = 'name';
    const FIELD_SALARY = 'salary';

    public function submit()
    {
        $this->emulateSubmit = true;
    }

    protected function getName()
    {
        return $this->getField(self::FIELD_NAME);
    }

    protected function checkSubmitted()
    {
        return $this->emulateSubmit === true;
    }

    protected function doProcess($submitted, $is_valid)
    {
        if ($submitted) {
            if ($is_valid) {
            } else {
                return $this->getMessages();
            }
        } else {
            if ($is_valid) {
                return $this->getName();
            } else {
                return false;
            }
        }
    }

    protected function getDefaultValues()
    {
        return array();
    }

    protected function validateState($submitted)
    {
        if ($submitted) {
            $this->assertNotBlank(self::FIELD_SALARY, 'no salary');

            return !$this->hasErrors();
        } else {
            return !empty($this->getName());
        }
    }
}
