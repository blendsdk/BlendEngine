<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Service\ControllerHandler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Blend\Framework\Service\ControllerHandler\ControllerHandler;

/**
 * Description of ControllerHandlerHtmlService
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ControllerHandlerHTMLService extends ControllerHandler {

    public function handle(Request $request, array $matchedRoute) {
        $result = parent::handle($request, $matchedRoute);
        if ($request instanceof Response) {
            return $result;
        } else {
            return new Response($result);
        }
    }

}
