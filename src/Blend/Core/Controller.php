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

/**
 * Base class for all Controllers in BlendEngine
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Controller {

    /**
     * @var Application
     */
    protected $application;

    public function __construct(Application $application) {
        $this->application = $application;
    }

}
