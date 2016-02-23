<?php print_php_header() ?>

namespace <?php echo $applicationNamespace;?>;

use Blend\Framework\Application\Application as Base;

/**
 * Description of <?php echo $applicationClassName;?>
 *
 * @author <?php echo $currentUserName;?> <<?php echo $currentUserEmail;?>>
 */
class <?php echo $applicationClassName;?> extends Base {

    protected $name = '<?php echo $applicationName?>';

}
