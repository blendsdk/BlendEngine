<?php print_php_header()?>

return array(
    'database' => array(
        'database' => '<?php echo $applicationDatabaseName?>',
        'username' => 'postgres',
        'password' => 'postgres'
    ),
    'email' => array(
        'host' => 'email.host.com',
        'port' => 465,
        'username' => 'username',
        'password' => 'password',
        'encryption' => 'ssl',
        'auth_mode' => 'login',
    ),
    'translation' => array(
        'defaultLocale' => 'en'
    ),
    'authmodule' => array(
        'login_path' => '/login',
        'logout_path' => '/logout',
        'after_logout_path' => '/'
    )
);
