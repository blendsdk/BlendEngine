<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Translation;

use Blend\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;

/**
 * TranslatorService is a customized Translator to be used in the application's
 * Service container.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TranslatorService extends Translator
{
    public function __construct($locale, $cacheDir = null, $debug = false)
    {
        parent::__construct($locale, new MessageSelector(), $cacheDir, $debug);
    }
}
