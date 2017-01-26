<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Translation;

use Blend\Component\DI\Container;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface TranslationProviderInterface
{
    public function configure(Translator $translator, Container $container);
}
