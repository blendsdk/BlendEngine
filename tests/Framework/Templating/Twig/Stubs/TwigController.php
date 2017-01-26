<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Templating\Twig\Stubs;

use Blend\Component\Templating\TemplateEngineInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of TwigController.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TwigController
{
    /**
     * @var TemplateEngineInterface
     */
    protected $templateEngine;

    public function __construct(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
        $this->templateEngine->setViewPaths(array(__DIR__ . '/../templates'));
    }

    public function urlTest(Request $request)
    {
        return $this->templateEngine->render('url.twig', array(
                    '_twig_trim' => true,
                    'request' => $request,
        ));
    }
}
