<?php echo "<?php\n" ?>

namespace <?php echo $table->getServiceNamespace() ?>;

use Blend\Database\Database;
use <?php echo $table->getServiceBaseNamespace()?>\<?php echo $table->getServiceClassName(); ?> as <?php echo $table->getServiceClassName(); ?>Base;

class <?php echo $table->getServiceClassName(); ?> extends <?php echo $table->getServiceClassName(); ?>Base {

}
