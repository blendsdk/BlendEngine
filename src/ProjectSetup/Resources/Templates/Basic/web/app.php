<?php print_php_header() ?>

include __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set("Europe/Amsterdam");

use <?php echo $applicationNamespace.'\\'.$applicationClassName;?>;
use Blend\Framework\Factory\ApplicationFactory;

(new ApplicationFactory(<?php echo $applicationClassName;?>::class, __DIR__ . '/..'))
        ->create()
        ->run();
