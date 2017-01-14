<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Application;

use Blend\Component\Templating\EngineInterface;
use Blend\Framework\Support\Runtime\RuntimeProviderInterface;

/**
 * Controller
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Controller {

    /**
     * @var EngineInterface
     */
    protected $renderer;

    /**
     * @var RuntimeProviderInterface;
     */
    protected $runtime;

    /**
     * Returns an array or a folder where the views are located
     */
    protected abstract function getTemplatesFolder();

    public function __construct(EngineInterface $renderer, RuntimeProviderInterface $runtime) {
        $this->renderer = $renderer;
        $this->runtime = $runtime;
        $viewsPath = $this->getTemplatesFolder();
        if(!is_array($viewsPath)) {
            $viewsPath = array($viewsPath);
        }
        $this->renderer->setViewPaths($viewsPath);
    }

}
