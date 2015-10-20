<?php echo "<?php\n" ?>

namespace <?php echo $table->getServiceBaseNamespace() ?>;

use Blend\Database\Database;
use Blend\Database\DatabaseService;
use <?php echo $table->getModelNamespace()?>\<?php echo $table->getModelClassName(); ?>;
use <?php echo $table->getSchemaNamespace(); ?>\<?php echo $table->getSchemaClassName() ?> as SC;

class <?php echo $table->getServiceClassName(); ?> extends DatabaseService {

    protected $recordClass;

    public function __construct(Database $database, $recordClass = null) {
        parent::__construct($database);
        $this->recordClass = is_null($recordClass) ? <?php echo $table->getModelClassName(); ?>::class : $recordClass;
    }

    /**
     * Deletes a <?php echo $table->getTableName() ?> record from the database
     * and returns the deleted record object
     * @return <?php echo $table->getModelClassName(); ?> The record that was deleted
     */
    public function <?php echo 'delete'?>(<?php echo $table->getModelClassName()?> &$object) {
        $object = $this->deleteObject(SC::TABLE_NAME, $object);
        return $object;
    }

    /**
     * Creates or updates <?php echo $table->getTableName() ?> record
     * and returns the newly or the updated record object
     * @return <?php echo $table->getModelClassName(); ?> The object that was saved
     */
    public function <?php echo 'save'?>(<?php echo $table->getModelClassName()?> &$object) {
        return $this->saveObject(SC::TABLE_NAME,$object);
    }


<?php foreach ($table->getKeys() as $keyName => $columns): ?>
    /**
     * @return <?php echo $table->getModelClassName(); ?>

     */
    public function <?php echo $table->getKeyFunctionName($keyName,'getBy');?>(<?php echo $table->getKeyGetterArgs($keyName);?>) {
        $params = array(<?php echo $table->getKeyQueryParams($keyName)?>);
        return $this->getObjectByParams(SC::TABLE_NAME, $params, $this->recordClass);
    }

    /**
     * Deletes a <?php echo $table->getTableName() ?> record from the database
     * and returns the deleted record object
     * @return <?php echo $table->getModelClassName(); ?> The record that was deleted
     */
    public function <?php echo $table->getKeyFunctionName($keyName,'deleteBy');?>(<?php echo $table->getKeyGetterArgs($keyName);?>) {
        $params = array(<?php echo $table->getKeyQueryParams($keyName)?>);
        return $this->deleteByParams(SC::TABLE_NAME, $params, $this->recordClass);
    }

<?php endforeach; ?>
}
