<?php print_php_header() ?>

namespace <?php echo $applicationNamespace;?>\Console;

use Blend\Component\Console\Application;

/**
 * Description of ApplicationCommand
 *
 * @author <?php echo $currentUserName;?> <<?php echo $currentUserEmail;?>>
 */
class <?php echo $applicationCommandClassName;?> extends Application {

    public function __construct($scriptname) {
        parent::__construct($scriptname, '<?php echo $applicationName?> Command Utility', '1.0');
    }

}
