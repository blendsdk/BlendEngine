<?php

include dirname(__FILE__) . '/../vendor/autoload.php';

use Blend\BlendUtility\Application as BlendUtilityApplication;

$app = new BlendUtilityApplication(__DIR__);
$app->run();
