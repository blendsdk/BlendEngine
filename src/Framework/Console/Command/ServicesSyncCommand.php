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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Blend\Component\Exception\InvalidConfigException;

/**
 * Description of ServicesSyncCommand
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ServicesSyncCommand extends Command {

    /**
     * @var string
     */
    private $arrayFile;

    /**
     * @var string
     */
    private $appFolder;

    protected function configure() {
        $this->setName('services:update')
                ->setDescription('Generates a new services.json file from a PHP array')
                ->addArgument('arrayFile', InputArgument::OPTIONAL, "The PHP array file to run");
    }

    public function setApplicationFolder($folder) {
        /**
         * This function is called internally by the InitCommand
         */
        $this->appFolder = $folder;
    }

    public function getServiceFile() {
        /**
         * This function is called internally by the InitCommand
         */
        return $this->arrayFile;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        if (!empty($this->appFolder)) {
            $this->container->setScalar('rootFolder', $this->appFolder);
        }

        $this->checkAndSetArrayFile();
        $info = pathinfo($this->arrayFile);
        $services = require($this->arrayFile);
        if (!is_array_assoc($services)) {
            throw new InvalidConfigException(
            "Looks the the {$info['basename']} did not return an associative array!"
            );
        }
        $this->output->writeln(
                "<info>The following services will be " .
                "availble from your application</info>");

        foreach ($services as $interface => $service) {
            $this->output->writeln("\t{$interface} => {$service}");
        }

        $outfile = $this->container->get('rootFolder') . '/config/services.json';
        $this->output->writeln("<info>Creating {$outfile}</info>");
        file_put_contents($outfile, json_encode($services, JSON_PRETTY_PRINT));
        $this->output->writeln("<info>Done.</info>");
    }

    private function checkAndSetArrayFile() {
        $arrayFile = $this->input->getArgument('arrayFile');
        if (empty($arrayFile)) {
            $arrayFile = $this->container->get('rootFolder') . '/resources/services.php';
        }

        if (!$this->fileSystem->exists($arrayFile)) {
            throw new FileNotFoundException("Unable to find a services.php, I looked for {$arrayFile}");
        }

        $this->arrayFile = $arrayFile;
    }

}
