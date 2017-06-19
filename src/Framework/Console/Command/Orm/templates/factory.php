<?php print_php_header() ?>

namespace <?php echo  $classNamespace ?>;

<?php foreach ($uses as $use): ?>
use <?php echo $use; ?>;
<?php endforeach; ?>

/**
 * <?php echo $className ?> is a data factory utility class for
 * the "<?php echo $classFQRN; ?>" relation
 */
<?php if (isset($classModifier)) echo $classModifier . ' ' ?>class <?php echo $className ?> extends <?php echo $classBaseClass; ?> {

<?php if(isset($extensionClass)) echo "    use " . $extensionClass . ";\n";?>

    /**
     * Class constructor
     * @param Database $database
     */
    public function __construct(Database $database) {
        parent::__construct($database, <?php echo $modelClass?>::class,'<?php echo $classFQRN?>');
    }

<?php foreach ($methods as $method_type => $method):?>
    /**
     * <?php echo $method['method_description']."\n"; ?>
<?php foreach ($method['parameters'] as $param_name => $param_type):?>
     * @param <?php echo(is_array($param_type) ? $param_type[0] : $param_type)."\t".$param_name."\n"?>
<?php endforeach; ?>
     */
    public function <?php echo $method['method_name']; ?>(<?php echo $method['method_call_params']?>) {
        <?php echo $method['method_content']."\n"?>
    }

<?php endforeach; ?>
}
