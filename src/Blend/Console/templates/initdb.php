<?php echo "<?php\n" ?>

namespace <?php echo $namespace ?>;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use <?php echo $namespace ?>\DatabaseInitDbCommandBase;
<?php foreach ($usages as $usage): ?>
use <?php echo $usage?>;
<?php endforeach; ?>

class DatabaseInitDbCommand extends DatabaseInitDbCommandBase {

}
