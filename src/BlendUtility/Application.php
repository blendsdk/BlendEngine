<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\BlendUtility;

use Blend\Component\Console\Application as ApplicationBase;

/**
 * Description of BlendUtilityApplication
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Application extends ApplicationBase {

    public function __construct($scriptname) {
        parent::__construct($scriptname, 'BlendEngine Command Console', '1.0');
    }

}
