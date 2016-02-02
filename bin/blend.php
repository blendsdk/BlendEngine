<?php

include dirname(__FILE__) . '/../vendor/autoload.php';

use Blend\Console\BlendUtilityApplication;
use Blend\Console\Command\ApplicationInitCommand;

$app = new BlendUtilityApplication();
$app->run();
