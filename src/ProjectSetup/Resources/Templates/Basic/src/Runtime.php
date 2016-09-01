<?php print_php_header() ?>

namespace <?php echo $applicationNamespace;?>;

use Blend\Framework\Support\Runtime\Runtime;

/**
 * The runtime setting class for <?php echo $applicationClassName;?>
 *
 * @author <?php echo $currentUserName;?> <<?php echo $currentUserEmail;?>>
 */
class <?php echo $applicationName;?>Runtime extends Runtime {

    protected $name = '<?php echo $applicationName?>';

}
