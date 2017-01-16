<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Templating\Twig\Stubs;

use Blend\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of TwigController
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigController {

    /**
     * @var EngineInterface
     */
    protected $templateEngine;

    public function __construct(EngineInterface $templateEngine) {
        $this->templateEngine = $templateEngine;
        $this->templateEngine->setViewPaths([__DIR__ . '/../templates']);
    }

    public function urlTest(Request $request) {
        return $this->templateEngine->render("url.twig", [
                    '_twig_trim' => true,
                    'request' => $request
        ]);
    }

}
