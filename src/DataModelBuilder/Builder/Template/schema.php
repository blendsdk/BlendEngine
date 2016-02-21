<?php print_php_header() ?>

namespace <?php echo $appNamespace . '\\' . $classNamespace ?>;

<?php foreach ($uses as $use): ?>
use <?php echo $use; ?>;
<?php endforeach; ?>

/**
 * <?php echo $className ?> is a helper class representng the "<?php echo $classFQRN; ?>" relation
 */
class <?php echo $className ?> extends <?php echo $classBaseClass; ?> {

    public function __construct($rel_alias) {
        parent::__construct('<?php echo $classFQRN; ?>', $rel_alias);
    }
<?php foreach ($props as $prop): ?>

    /**
     * The <?php echo $prop['name']?> column
     * @return SQLString
     */
    public function <?php echo $prop['getter']?>() {
        return $this->column('<?php echo $prop['name']?>');
    }
<?php endforeach; ?>

}
