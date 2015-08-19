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
use Blend\Core\Module;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @var Module
     */
    protected $module;

    public function __construct(Application $application, Module $module) {
        $this->application = $application;
        $this->module = $module;
    }

    public function prepareAction(Request $request) {
        return;
    }

}
