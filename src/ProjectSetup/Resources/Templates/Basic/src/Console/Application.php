<?php print_php_header() ?>

namespace <?php echo $applicationNamespace; ?>\Console;

use Blend\Framework\Console\Application;
use Blend\Framework\Console\Command\ServeCommand;
use <?php echo $applicationNamespace; ?>\Console\Command\PublishCommand;

/**
 * ApplicationCommand
 *
 * @author <?php echo $currentUserName; ?> <<?php echo $currentUserEmail; ?>>
 */
class <?php echo $applicationCommandClassName; ?> extends Application {

    public function __construct($scriptPath) {
        parent::__construct($scriptPath, '<?php echo $applicationName?> Command Utility', '1.0');
        $this->add(new ServeCommand());
        $this->add(new PublishCommand());
    }

}
