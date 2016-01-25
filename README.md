# BlendEngine

### PLEASE NOTE: This project is in development and not ready to be used in production.

BlendEngine is a web application framework written using Symfony Components.
It trimmed down to  sit somewhere between Silex and Symfony. It has several core
components for creating web applications which have a public facing front-end
and a back-office application.

Current built-in functionality:

    - No dependency injection: the core application has several built-in services

    - Routing: similar to Symfony and Silex

    - Modules: generic solution similar to Bundles in Symfony

    - Translation: for making the application multi-lingual

    - PostgreSQL: We only support PostgreSQL for obvious reasons

    - Data Models: Generic data mapper as ORM, not doctrine

    - Twig: Support for Twig template engine

    - Array configuration: configuration files are in PHP arrays, not .yml

    - PDF generation: Helps generating PDF files using wkhtml2pdf library

    - SwiftMailer: Provides functionality to send e-mails

    - Session Handling: Native (php native) session handling

    - Text and JSON Response: Creates the correct HTTP response based on
        controller/action return values

    - Security: Simplified security handling form based authentication

    - Event Dispatching: Same as Silex and Symfony


