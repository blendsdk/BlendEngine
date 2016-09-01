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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\Event;
use Blend\Component\DI\Container;

/**
 * Description of GetRequest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class KernelEvent extends Event {
    
    /**
     * @var Request 
     */
    protected $request;
    /**
     * @var Container 
     */
    protected $container;
    
    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }
    
    /**
     * @return Container
     */
    public function getContainer() {
        return $this->container;
    }

    public function __construct(Request $request,  Container $container) {
        $this->request = $request;
        $this->container = $container;
    }
    
}
