<?php print_php_header() ?>

include __DIR__ . '/../vendor/autoload.php';

// This check prevents access to debug front controllers that are deployed
// by accident to production servers. Feel free to remove this, extend it,
// or make something more sophisticated.

if (
        isset($_SERVER['HTTP_CLIENT_IP']) ||
        isset($_SERVER['HTTP_X_FORWARDED_FOR']) ||
        !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', 'fe80::1', '::1']) ||
        php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '
            . basename(__FILE__) . ' for more information.');
}

error_reporting(-1);
ini_set('display_errors', 'On');

date_default_timezone_set("Europe/Amsterdam");

use <?php echo $applicationNamespace.'\\'.$applicationClassName;?>;
use Blend\Framework\Factory\ApplicationFactory;

(new ApplicationFactory(<?php echo $applicationClassName;?>::class, __DIR__ . '/..',true))
        ->create()
        ->run();
