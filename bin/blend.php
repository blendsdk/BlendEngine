<?php

include dirname(__FILE__) . '/../vendor/autoload.php';

use Blend\ProjectSetup\SetupApplication;

$app = new SetupApplication(__DIR__);
$app->run();
