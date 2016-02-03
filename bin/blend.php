<?php

include dirname(__FILE__) . '/../vendor/autoload.php';

use Blend\ProjectSetup\ProjectSetupApplication;

$app = new ProjectSetupApplication(__DIR__);
$app->run();
