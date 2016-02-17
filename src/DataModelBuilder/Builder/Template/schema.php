<?php print_php_header() ?>

namespace <?php echo $appNamespace . '\\' . $classNamespace ?>;

/**
 * <?php echo $className ?> is a helper class representng the "<?php echo $classFQRN; ?>" relation
 */
class <?php echo $className ?> {

    /**
     * @var string the sys_actuall_planning_detail_view schema
     */
    const TABLE_NAME = '<?php echo $classFQRN;?>';
<?php foreach ($props as $prop): ?>

    /**
     * @var <?php echo str_replace(' ','&nbsp;',$prop['type'])?> the <?php echo $prop['column']?> column
     */
    const <?php echo $prop['name']?> = '<?php echo $prop['column']?>';
<?php endforeach; ?>

}
