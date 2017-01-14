<?php print_php_header() ?>

namespace <?php echo $applicationNamespace;?>\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Blend\Framework\Console\Command\PublishCommand as Base;

/**
 * @author <?php echo $currentUserName;?> <<?php echo $currentUserEmail;?>>
 */
class PublishCommand extends Base {
    //put your code here
    protected function publish(InputInterface $input, OutputInterface $output) {

    }

}
