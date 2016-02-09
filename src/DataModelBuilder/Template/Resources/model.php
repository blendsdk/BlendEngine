<?php print_php_header() ?>

namespace <?php echo $namespace ?>;

<?php foreach ($uses as $use): ?>
use <?php echo $use; ?>;
<?php endforeach; ?>

/**
 * Represents a record from the "<?php echo $fqrn ?>" relation
 */
<?php if (isset($classModifier)) echo $classModifier . ' '; ?>class <?php echo $className ?> extends <?php echo $baseClass; ?> {

<?php if (isset($props)) foreach ($props as $prop): ?>
    /**
     * Gets the value of the <?php echo "'{$prop['name']}' column.\n"?>
     * @param <?php echo $prop['type']?> $default
     * @return <?php echo $prop['type']."\n"?>
     */
    function <?php echo $prop['getter'] ?>($default = null) {
        return $this->getValue('<?php echo $prop['name'] ?>', $default);
    }

    /**
     * Sets the value of the <?php echo "'{$prop['name']}' column.\n"?>
     * @param <?php echo $prop['type']?> $value
     * @return \<?php echo $namespace ?>\<?php echo $className."\n" ?>
     */
    function <?php echo $prop['setter'] ?>($value) {
        return $this->setValue('<?php echo $prop['name'] ?>', $value);
    }

<?php endforeach; ?>
}
