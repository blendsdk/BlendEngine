<?php echo "<?php\n" ?>

namespace <?php echo $namespace ?>;

use Blend\Web\Application;
<?php foreach ($usages as $usage): ?>
use <?php echo $usage?>;
<?php endforeach; ?>

/**
 * @abstract
 */
abstract class <?php echo $class_name ?> extends Application {

<?php foreach ($properties as $property): ?>
    const <?php echo $property['const_name']?> = '<?php echo $property['const_name']?>';
<?php endforeach; ?>

<?php foreach ($properties as $property): ?>
    /**
     * Get the <?php echo $property['type']?> for handling <?php echo $property['table']->getTableName()?> records
     * @return <?php echo $property['type']?>

     */
    public function get<?php echo ucfirst($property['name']);?>() {
        $service = $this->getService(self::<?php echo $property['const_name']?>);
        if (is_null($service)) {
            return $this->registerService(self::<?php echo $property['const_name']?>, new <?php echo $property['type']?>($this->getDatabase()));
        } else {
            return $service;
        }
    }

<?php endforeach; ?>
}
