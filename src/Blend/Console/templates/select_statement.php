<?php echo "<?php\n" ?>

namespace <?php echo $namespace ?>;

<?php foreach ($uses as $use): ?>
use <?php echo $use?>;
<?php endforeach; ?>

/**
 * <?php echo $description;?>

 */
class <?php echo $classname?> extends SelectStatement {
<?php foreach ($setters as $setter): ?>

    /**
     * @param <?php echo $setter['type']?> $value
     */
    public function set<?php echo $this->ucWords($setter['name']);?>($value) {
        $this->setParameterValue('<?php echo $setter['param']?>', $value);
    }
<?php endforeach; ?>

    protected function buildSQL() {
        return "<?php echo $sql?>";
    }

}
