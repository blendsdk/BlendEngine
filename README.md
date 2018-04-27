# BlendEngine

### PLEASE NOTE: This project is in development and not ready to be used in production.

BlendEngine is a web application framework written using Symfony Components.
Customized to sit somewhere between Silex and Symfony. It has several core
components for creating web applications and public facing websites.

### Current built-in functionality:

    - Dependency Injection Container, and a Service Container.

    - Routing: similar to Symfony and Silex.

    - Modules: generic solution similar to Bundles in Symfony.

    - Translation: for making the application multi-lingual.

    - PostgreSQL: We only support PostgreSQL for obvious reasons.

    - Data Models: Generic data mapper and builder for ORM, not Doctrine!

    - Template: Support for Twig, and Raw PHP templates.

    - JSON configuration: configuration files are in JSON, not .yml.

    - PDF generation: Helps generating PDF files using wkhtml2pdf library.

    - SwiftMailer: Provides functionality to send e-mails.

    - Session Handling: Native (php native) session handling by default but
        customizable to use Redis or Memcache

    - Text and JSON Response: Creates the correct HTTP response based on
        controller/action return values

    - Security: Simplified security handling for Form based authentication.

    - Roles: Simplified Role handling to tie a Route to a role

    - Event Dispatching: Same as Silex and Symfony

    - Form Processing: Easy Form processing, included built-in POST->Redirect-GET

    - JSON API: Built-in functionality to create JSON (REST-ish) APIs

## Requirements

BlendEngine requires the following componenets:

     - PHP 5.6    : Is required and must be available from the command prompt.
     - composer   : Is required and must be available from the command prompt.
     - compass    : Required if you want to be able to compile the sass files to css
     - PostgreSQL : Required if your application needs to work with a database (We don't support MySQL)
