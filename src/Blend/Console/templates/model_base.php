<?php echo "<?php\n" ?>

namespace <?php echo $table->getModelBaseNamespace() ?>;

use Blend\Database\Model;
use <?php echo $table->getSchemaNamespace() . "\\" . $table->getSchemaClassName(); ?> as SC;

/**
 * @abstract
 */
abstract class <?php echo $table->getClassName(); ?> extends Model {

    public function __construct($record = array()) {
        $this->values = array();
        $this->initial = array(
<?php foreach ($table->getColumns() as $column): ?>
            SC::<?php echo $column->getColumnName(true)?> => true,
<?php endforeach; ?>
        );
        parent::__construct($record);
    }
<?php foreach ($table->getColumns() as $column): ?>

    /**
     * Getter for the <?php echo $column->getColumnName(); ?> column
     * @param mixed $default defaults to null
     * @return <?php echo $column->getDataType(); ?>

     */
    public function <?php echo $column->getColumnFunctionName('get'); ?>($default = null) {
        return $this->getValue(SC::<?php echo $column->getColumnName(true);?>, $default);
    }

    /**
     * Setter for the <?php echo $column->getColumnName(); ?> column
     * @param mixed $value
     * @return <?php echo $table->getClassName(); ?>

     */
    public function <?php echo $column->getColumnFunctionName('set'); ?>($value) {
        return $this->setValue(SC::<?php echo $column->getColumnName(true);?>, $value);
    }
<?php endforeach; ?>

}
