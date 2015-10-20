<?php echo "<?php\n" ?>

namespace <?php echo $table->getModelNamespace() ?>;

use <?php echo $table->getModelBaseNamespace() . "\\" . $table->getClassName(); ?> as Base;

class <?php echo $table->getClassName(); ?> extends Base {

}
