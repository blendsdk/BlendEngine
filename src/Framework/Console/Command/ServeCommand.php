<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Console\Command;

use Blend\Framework\Console\Command\Command;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Input\InputOption;

/**
 * Description of ServeCommand
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ServeCommand extends Command {

    protected function configure() {
        $this->setName('serve')
                ->setDescription('Serve the application on the PHP development server')
                ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on.');
    }

}
