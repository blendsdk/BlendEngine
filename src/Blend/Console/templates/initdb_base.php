<?php echo "<?php\n" ?>

namespace <?php echo $namespace ?>;

use Blend\Console\DatabaseConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
<?php foreach ($usages as $usage): ?>
use <?php echo $usage?>;
<?php endforeach; ?>

/**
 * @abstract
 */
abstract class DatabaseInitDbCommandBase extends DatabaseConsoleCommand {

<?php foreach ($properties as $property): ?>
    /**
     * @var <?php echo $property['type']?>

     */
    protected $<?php echo $property['name']?>;

<?php endforeach; ?>
    protected abstract function buildDatabase();

    protected function configure() {
        parent::configure();
        $this->setName('database:initdb')
                ->setDescription('Initializes the database with master data');
    }

    protected function getApplicationName() {
        return '<?php echo $application_name?>';
    }

    protected function getConfigFolderLocation() {
        return dirname(__FILE__) . '/../../config';
    }

    protected function executeDatabaseOperation(InputInterface $input, OutputInterface $output) {
        $this->initServices();
        $this->buildDatabase();
    }

    protected function initServices() {

<?php foreach ($properties as $property): ?>
        $this-><?php echo $property['name']?> = new <?php echo $property['type']?>($this->database);
<?php endforeach; ?>
    }

}
