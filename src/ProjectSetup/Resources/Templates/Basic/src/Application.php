<?php print_php_header() ?>

namespace <?php echo $applicationNamespace; ?>;

use Blend\Framework\Application\Application<?php echo $testable == true ? 'Testable' : '' ?> as Base;
use Blend\Framework\Support\Runtime\RuntimeProviderInterface;
use Blend\Component\DI\ServiceContainer;
use <?php echo $applicationNamespace; ?>\<?php echo $applicationName; ?>Runtime;


/**
 * The <?php echo $applicationClassName; ?> application class
 *
 * @author <?php echo $currentUserName; ?> <<?php echo $currentUserEmail; ?>>
 */
class <?php echo $applicationClassName; ?> extends Base {

    protected function confiureServices(ServiceContainer $container) {
        $container->loadServices(array(
            /*
             * DO NOT REMOVE THE RUNTIME PROVIDER
             */
             RuntimeProviderInterface::class => <?php echo $applicationName; ?>Runtime::class,
            /*
             * THE ACME MODULE
             */
            "acme-module" => \Acme\Acme::class
        ));
    }

}
