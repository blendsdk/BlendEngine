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

use Blend\Component\DI\Container;
use Symfony\Component\Translation\TranslatorInterface;

/**
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface TranslationProviderInterface {

    public function configure(TranslatorInterface $translator, Container $container);
}
