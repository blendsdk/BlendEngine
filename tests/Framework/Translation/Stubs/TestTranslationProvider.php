<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Translation\Stubs;

use Blend\Component\Translation\TranslationProviderInterface;
use Blend\Component\Translation\Translator;
use Blend\Component\DI\Container;
use Symfony\Component\Translation\Loader\ArrayLoader;

/**
 * Description of TestTranslationProvider
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TestTranslationProvider implements TranslationProviderInterface {

    public function configure(Translator $translator, Container $container) {
        if (!$translator->hasLoader('array')) {
            $translator->addLoader('array', new ArrayLoader());
        }
        $resource = [
            'Good morning :name!' => 'Bari louys :name!'
        ];
        $translator->addResource('array', $resource, 'am');
    }

}
