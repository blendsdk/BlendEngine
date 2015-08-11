<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Core;

use Blend\Core\Application;
use Symfony\Component\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\MessageSelector;

/**
 * Translator that gets the current locale from the BlendEngine application.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Translator extends BaseTranslator {

    protected $application;

    public function __construct(Application $application, MessageSelector $selector, $cacheDir = null, $debug = false) {
        $this->application = $application;
        parent::__construct(null, $selector, $cacheDir, $debug);
    }

    public function getLocale() {
        return $this->application->getLocale();
    }

    public function setLocale($locale) {
        if (null === $locale) {
            return;
        }
        $this->application->setLocale($locale);
        parent::setLocale($locale);
    }

}
