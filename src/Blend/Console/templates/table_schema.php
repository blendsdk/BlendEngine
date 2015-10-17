<?php echo "<?php\n" ?>

namespace <?php echo $namespace ?>;

class <?php echo $class_name; ?> {

    const PRIMARY_KEY = array(<?php echo implode(', ',$primary_key) ?>);
<?php foreach ($columns as $column): ?>

    /**
     * @var <?php echo $column['data_type'] ?> <?php echo $column['description'] ?>

     */
    const <?php echo $column['column_name_upr']; ?> = '<?php echo $column['column_name']; ?>';
<?php endforeach; ?>

}
