<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Web\Twig\Filters;

/**
 * Description of BlendEngine
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class BlendEngineExtension extends \Twig_Extension {

    public function getName() {
        return 'blend-engine';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('nl_euro', array($this, 'nlEuro'),array('is_safe' => array('all'))),
        );
    }

    public function nlEuro($number, $sign = true, $decimals = 2) {
        $result = number_format($number, $decimals, ',', '.');

        return ($sign === true ? '&euro;' : '') . str_replace(',00', ',-', $result);
    }

}
