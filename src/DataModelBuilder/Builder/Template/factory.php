<?php print_php_header() ?>

namespace <?php echo $appNamespace . '\\' . $classNamespace ?>;

<?php foreach ($uses as $use): ?>
use <?php echo $appNamespace . '\\' . $use; ?>;
<?php endforeach; ?>

<?php if (isset($classModifier)) echo $classModifier . ' '; ?>class <?php echo $className ?> extends <?php echo $classBaseClass; ?> {

}
