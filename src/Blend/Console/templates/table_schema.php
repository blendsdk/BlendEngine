<?php echo "<?php\n" ?>

namespace <?php echo $table->getSchemaNamespace() ?>;

class <?php echo $table->getSchemaClassName(); ?> {

    /**
     * @var string the <?php echo $table->getTableName(); ?> schema
     */
    const TABLE_NAME = '<?php echo $table->getTableName(); ?>';
<?php foreach ($table->getColumns() as $column): ?>

    /**
     * @var <?php echo $column->getDataType() ?> <?php echo $column->getDescription() ?>

     */
    const <?php echo $column->getColumnName(true); ?> = '<?php echo $column->getColumnName(); ?>';
<?php endforeach; ?>

}
