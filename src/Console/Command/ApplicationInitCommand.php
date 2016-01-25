<?php

namespace Blend\Console\Command;

use Blend\Console\Command\CommandBase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplicationInitCommand extends CommandBase {

  protected function configure() {

      $this
          ->setName('application:init')
          ->setDescription('Initializes the app folder as a Blend application ('.$this->getAppFolder().')');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
  }

}
