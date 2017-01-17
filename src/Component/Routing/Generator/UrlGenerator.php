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

use Blend\Component\Routing\RouteAttribute;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator as GeneratorBase;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Customized UrlGenerator to include the current Locale.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class UrlGenerator extends GeneratorBase
{
    private $locale;

    public function __construct(RouteCollection $routes, RequestContext $context, LoggerInterface $logger = null, $_locale = null)
    {
        /*
         * The $_locale parameter is automatically retrieved from the container
         * if the LocaleService is available
         */
        parent::__construct($routes, $context, $logger);
        $this->locale = $_locale;
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if ($this->locale !== null && !isset($parameters[RouteAttribute::LOCALE])) {
            $parameters[RouteAttribute::LOCALE] = $this->locale;
        }

        return parent::generate($name, $parameters, $referenceType);
    }
}
