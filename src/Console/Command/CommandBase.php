<?php

namespace Blend\Console\Command;

use Symfony\Component\Console\Command\Command;

abstract class CommandBase extends Command {

  protected function getAppFolder($append='') {
    $hostFolder = realpath(dirname(__FILE__).'/../../../../../../');
    return $hostFolder.$append;
  }


}
