<?php echo "<?php\n" ?>

namespace <?php echo $namespace ?>;

use <?php echo $schema_namespace . "\\" . $schema_class_name; ?> as SC;

class <?php echo $class_name; ?> {

    protected $fields;
    protected $values;

    public function __construct($record = array()) {
        $this->values = array();
        $this->fields = array(
<?php foreach ($columns as $column): ?>
            SC::<?php echo $column['column_name_upr']?> => true,
<?php endforeach; ?>
        );
        foreach ($record as $key => $value) {
            if (isset($this->fields[$key])) {
                $this->values[$key] = $value;
            }
        }
    }
<?php foreach ($columns as $column): ?>

    /**
     * Getter for the <?php echo $column['column_name']; ?> column
     * @param mixed $default, defaults to null
     * @return <?php echo $column['data_type'] ?>

     */
    public function <?php echo $column['column_name_getter_name']; ?>($default = null) {
        return isset($this->values[SC::<?php echo $column['column_name_upr']?>]) ? $this->values[SC::<?php echo $column['column_name_upr']?>] : $default;
    }
<?php endforeach; ?>

}
