<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Service;

use Blend\Core\Application;

/**
 * Base class for all Application level services Service
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Service {

    /**
     * @var Application
     */
    protected $application;

    public function __construct(Application $application) {
        $this->application = $application;
    }

}
