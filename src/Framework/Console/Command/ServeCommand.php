<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessUtils;

/**
 * ServeCommand.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ServeCommand extends Command
{
    protected function configure()
    {
        $port = $this->findNextAvailablePort('0.0.0.0');
        $this->setName('serve')
                ->setDescription('Serve the application on the PHP development server')
                ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'The port to serve the application on.', $port)
                ->addOption('host', 'H', InputOption::VALUE_OPTIONAL, 'The host address to serve the application on.', '0.0.0.0');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $base = ProcessUtils::escapeArgument($this->getApplication()->getProjectFolder());
        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder())->find(false));

        if (($scriptName = $this->findApplication()) !== false) {
            $host = $input->getOption('host');
            $port = $input->getOption('port');
            $output->writeln("<warn>Running the application on $host:$port using $scriptName</warn>");
            passthru("{$binary} -S {$host}:{$port} {$base}/web/$scriptName");
        }
    }

    /**
     * Try to find the application script using the config.
     *
     * @return string|bool
     */
    private function findApplication()
    {
        $name = $this->getConfig('name', null);
        if (!is_null($name)) {
            $scriptName = strtolower($name).'_dev.php';
            $file = $this->getApplication()->getProjectFolder()
                    .'/web/'
                    .$scriptName;
            if (file_exists($file)) {
                return $scriptName;
            } else {
                $this->output->writeln("<error>Unable to find the $file!");

                return false;
            }
        } else {
            $this->output->writeln('<error>No application name is found in the config.json file!');

            return false;
        }
    }

    /**
     * Find the next open port.
     *
     * @param type $host
     *
     * @return int
     */
    private function findNextAvailablePort($host)
    {
        for ($port = 8000; $port != 9000; ++$port) {
            $connection = @fsockopen($host, $port);
            if (is_resource($connection)) {
                fclose($connection);
            } else {
                return $port;
            }
        }
    }
}
