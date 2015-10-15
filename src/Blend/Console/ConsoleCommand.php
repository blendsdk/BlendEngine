<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Console;

use Blend\Core\Environments;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for all Console commands
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ConsoleCommand extends Command {

    /**
     * @var string
     */
    protected $env;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected abstract function executeInternal(InputInterface $input, OutputInterface $output);

    protected abstract function getApplicationName();

    protected abstract function getConfigFolderLocation();

    protected function configure() {
        $this->addOption('environment', 'e', InputOption::VALUE_OPTIONAL, 'Configuration environment (' . Environments::PRODUCTION . ' or ' . Environments::DEVELOPMENT . ')', Environments::PRODUCTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->input = $input;
        $this->output = $output;

        if ($this->initEnvironment()) {
            $this->executeInternal($input, $output);
        }
    }

    /**
     * Initializes the environment variable
     * @return boolean
     */
    private function initEnvironment() {
        $options = array(Environments::DEVELOPMENT, Environments::PRODUCTION);
        $env = $this->input->getOption('environment');
        if (in_array($env, $options)) {
            $this->env = $env;
            return true;
        } else {
            $this->output->writeln('<error>Invalid environment option!</error>');
            return false;
        }
    }

    protected function renderFile($_viewFile_, $_data_ = null, $_return_ = true) {
        if (is_array($_data_))
            extract($_data_, EXTR_PREFIX_SAME, 'data');
        else
            $data = $_data_;
        if ($_return_) {
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);
            return ob_get_clean();
        } else
            require($_viewFile_);
    }

}
