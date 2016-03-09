<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGenerator as GeneratorBase;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Psr\Log\LoggerInterface;

/**
 * Customized UrlGenerator to include the current Locale
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class UrlGenerator extends GeneratorBase {

    private $locale;

    public function __construct(RouteCollection $routes, RequestContext $context, LoggerInterface $logger = null, $_locale = null) {
        /**
         * The $_locale parameter is automatically retrived from the container
         * if the LocaleService is availble
         */
        parent::__construct($routes, $context, $logger);
        $this->locale = $_locale;
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH) {
        if ($this->locale !== null && !isset($parameters['_locale'])) {
            $parameters['_locale'] = $this->locale;
        }
        return parent::generate($name, $parameters, $referenceType);
    }

}
