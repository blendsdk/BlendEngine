<?php print_php_header() ?>

namespace <?php echo $appNamespace . '\\' . $classNamespace ?>;

<?php foreach ($uses as $use): ?>
use <?php echo $use; ?>;
<?php endforeach; ?>

/**
 * <?php echo $className ?> is a data factory utility class for
 * the "<?php echo $classFQRN; ?>" relation
 */
<?php if (isset($classModifier)) echo $classModifier . ' '; ?>class <?php echo $className ?> extends <?php echo $classBaseClass; ?> {

<?php if (isset($generate)):?>
    public function __construct(Database $database) {
        parent::__construct($database, '<?php echo $modelClass?>');
    }
<?php endif;?>
}
