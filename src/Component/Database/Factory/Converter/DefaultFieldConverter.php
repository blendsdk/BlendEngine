<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Database\Factory\Converter;

/**
 * DefaultFieldConverter.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DefaultFieldConverter extends FieldConverter
{
    const CONVERT_EMAIL_FIELD = 1000;
    const CONVERT_PASSWORD = 1001;
    const CONVERT_OBJECT = 1002;

    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->addConverter(self::CONVERT_EMAIL_FIELD, new ToLowerCaseConverter());
        $this->addConverter(self::CONVERT_PASSWORD, new PasswordConverter());
        $this->addConverter(self::CONVERT_OBJECT, new ObjectConverter());
    }
}
