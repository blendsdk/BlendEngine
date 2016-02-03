<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Blend\Component\DI\Container;
use Blend\Component\Configuration\Configuration;

/**
 * CommandBase in the base class for all application level command in 
 * BlendEngine.
 * 
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class CommandBase extends Command {

    /**
     * DI Container for handling services and object
     * @var Container; 
     */
    protected $container;

    public function __construct($name = null) {
        parent::__construct($name);
        $this->container = new Container();
        $this->initContainer();
    }

    /**
     * Initializes the DI container
     */
    protected function initContainer() {
        $this->container->singleton('config', [
            'class' => Configuration::class,
            'fname' => $this->getApplication()->getProjectFolder()
        ]);
    }

    /**
     * Returns a configuration variable
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getConfig($name, $default = null) {
        /* @var $config Configuration */
        $config = $this->container->get('config');
        return $config->get($name, $default);
    }

}
