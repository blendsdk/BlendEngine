<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Translation;

use Symfony\Component\Translation\Translator as TranslatorBase;

/**
 * Customized Translator class.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Translator extends TranslatorBase
{
    /**
     * Check if a given loader exists.
     *
     * @param type $format
     *
     * @return type
     */
    public function hasLoader($format)
    {
        return array_key_exists($format, $this->getLoaders());
    }
}
