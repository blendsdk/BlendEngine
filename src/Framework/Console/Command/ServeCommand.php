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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of ServeCommand
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ServeCommand extends Command {

    protected function configure() {
        $port = $this->findNextAvailablePort('0.0.0.0');
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
                        , '0.0.0.0')
                ->addOption('name'
                        , 'n'
                        , InputOption::VALUE_OPTIONAL
                        , 'Name of the application to run'
                        , strtolower($this->getConfig('name')));
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $base = ProcessUtils::escapeArgument($this->getApplication()->getProjectFolder());
        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));

        if (($scriptName = $this->findApplication($input->getOption('name'))) !== false) {
            $host = $input->getOption('host');
            $port = $input->getOption('port');
            $output->writeln("<warn>Running the application on $host:$port</warn>");
        } else {
            $output->writeln("<error>Unable to find the $scriptName!");
        }
    }

    private function findApplication($name) {
        $sctipyName = $name . '_dev.php';
        $file = $this->getApplication()->getProjectFolder()
                . '/web/'
                . $sctipyName;
        if (file_exists($sctipyName)) {
            return $sctipyName;
        } else {
            return false;
        }
    }

    /**
     * Find the next open port
     * @param type $host
     * @return int
     */
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
