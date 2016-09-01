<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\HttpKernel\Event;
use Blend\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Blend\Component\DI\Container;

/**
 * Description of GetExceptionResponseEvent
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class GetExceptionResponseEvent extends GetResponseEvent {
    
    /**
     * @var \Exception 
     */
    private $exception;
    
    /**
     * @return \Exception
     */
    public function getEception() {
        return $this->exception;
    }
    
    public function __construct(Request $request, Container $container,  \Exception $exception) {
        parent::__construct($request, $container);
        $this->exception = $exception;
    }    
    
}
