<?php

include dirname(__FILE__).'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Blend\Console\Command\ApplicationInitCommand;

$app = new Application('BlendEngine Command Console', '1.0');
$app->add(new ApplicationInitCommand());
$app->run();
