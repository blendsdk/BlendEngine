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
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Input\InputOption;

/**
 * Description of ServeCommand
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ServeCommand extends Command {

    protected $port;

    protected function configure() {
        $this->port = $this->findNextAvailablePort('0.0.0.0');
        $this->setName('serve')
                ->setDescription('Serve the application on the PHP development server')
                ->addOption('port'
                        , 'p'
                        , InputOption::VALUE_OPTIONAL
                        , 'The port to serve the application on.', $this->port)
                ->addOption('host'
                        , 'H'
                        , InputOption::VALUE_OPTIONAL
                        , 'The host address to serve the application on.'
                        , '0.0.0.0');
    }

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
        $base = ProcessUtils::escapeArgument($this->getApplication()->getProjectFolder());
        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));
    }

    private function findNextAvailablePort($host) {
        for ($port = 8000; $port != 9000; $port++) {
            $connection = @fsockopen($host, $port);
            if (is_resource($connection)) {
                fclose($connection);
            } else {
                return $port;
            }
        }
    }

}
