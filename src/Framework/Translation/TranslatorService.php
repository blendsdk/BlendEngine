<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Translation;

use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator as TranslatorBase;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * Description of Translator
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TranslatorService extends TranslatorBase {

    public function __construct($locale, $cacheDir = null, $debug = false) {
        parent::__construct($locale, new MessageSelector(), $cacheDir, $debug);
    }

    public function addLoader($format, LoaderInterface $loader) {
        /**
         * Override to skip the existsing loaders
         */
        if (!array_key_exists($format, $this->getLoaders())) {
            parent::addLoader($format, $loader);
        }
    }

}
