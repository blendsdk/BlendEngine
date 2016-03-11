<?php print_php_header() ?>

use Acme\Acme;
use Blend\Component\Templating\EngineInterface;
use Blend\Framework\Templating\Twig\TwigEngineService;
use Blend\Framework\Locale\LocaleService;
use Blend\Component\Database\Database;
use Blend\Framework\Factory\DatabaseFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Blend\Framework\Factory\TranslatorFactory;

return [
    /**
     * <?php echo $applicationName;?>Runtime will help you to gain access 
     * to the application's Dependency Injection Container. 
     * DO NOT REMOVE THIS SERVICE! YOUR APPLICATION MIGHT STOP WORKING!
     */
    "<?php echo $applicationNamespace;?>\\<?php echo $applicationName;?>Runtime" => "<?php echo $applicationNamespace;?>\\<?php echo $applicationName;?>Runtime",
    
    /**
     * Example of how you can add your custom modules to your application.
     */
    "acme-module" => Acme::class,
    
    /**
     * Uncomment if you need multi-language option in your application.
     * The LocaleService will automatically handle the {_locale}
     * Route arguments when this service en enabled
     */
    //"locale-service" => LocaleService::class,

    /**
     * Uncomment if your are going to use text translation in your application
     * The Translator need the LocaleService::class to be available
     */
    //TranslatorInterface::class => TranslatorFactory::class,

    /**
     * Uncomment to enable PostgreSQL database connectivity
     * check the config.json->database
     */
    //Database::class => DatabaseFactory::class,
    EngineInterface::class => TwigEngineService::class
];

