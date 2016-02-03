<?php print_php_header();?>

include dirname(__FILE__) . '/../vendor/autoload.php';

use <?php echo $applicationNamespace;?>\Console\<?php echo $applicationCommandClassName?>;

$app = new <?php echo $applicationCommandClassName?>(__DIR__);
$app->run();
