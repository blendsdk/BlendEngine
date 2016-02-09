<?php print_php_header() ?>

namespace <?php echo $namespace ?>;

<?php foreach ($uses as $use): ?>
use <?php echo $use; ?>;
<?php endforeach; ?>

/**
 * Factory from the "<?php echo $fqrn ?>" relation
 */
<?php if (isset($classModifier)) echo $classModifier . ' '; ?>class <?php echo $className ?> extends <?php echo $baseClass; ?> {

}
