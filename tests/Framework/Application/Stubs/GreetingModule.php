<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Framework\Application\Stubs;

use Blend\Component\Routing\RouteProviderInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Blend\Tests\Framework\Application\Stubs\GreetingController;
use Blend\Component\Configuration\Configuration;

/**
 * Description of GreetingRoutes
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class GreetingModule implements RouteProviderInterface {
    
    private $config;
    
    public function __construct(Configuration $config) {
        $this->config = $config;
    }

    public function loadRoutes(RouteCollection $collection) {
        
        $collection->add('hello', new Route('/hello', [
            'name' => 'World',
            'controller' => [GreetingController::class,'hello']
        ]));
        
        $collection->add('/', new Route('/', [
            'controller' => [GreetingController::class,'index'],
            '_locale_' => $this->config->get('translation.defaultLocale')
        ]));
    }

//put your code here
}
