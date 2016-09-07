<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Configuration;

/**
 * Interface for creating a translation configuration
 * 
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
interface TranslationConfigurationInterface {

    function getTranslationDefaultLocale();

    function getTranslationAvailableLocales();

    function getTranslationPersistInSession();
}
