<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Templating\Twig\Extension;

/**
 * EuroCurrencyExtension provides converting an rendering of the
 * euro currency numbers.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class EuroCurrencyExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'euro-currency';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('euro', array($this, 'euroCurrency'), array('is_safe' => array('all'))),
        );
    }

    public function euroCurrency($number, $sign = true, $decimals = 2)
    {
        $result = number_format($number, $decimals, ',', '.');

        return ($sign === true ? '&euro;' : '').str_replace(',00', ',-', $result);
    }
}
