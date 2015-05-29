LexikMonologBrowserBundle
=========================

[![Build Status](https://secure.travis-ci.org/lexik/LexikMonologBrowserBundle.png)](http://travis-ci.org/lexik/LexikMonologBrowserBundle)
![Project Status](http://stillmaintained.com/lexik/LexikMonologBrowserBundle.png)
[![Latest Stable Version](https://poser.pugx.org/lexik/monolog-browser-bundle/v/stable)](https://packagist.org/packages/lexik/monolog-browser-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d73bade1-4158-4085-aab8-1042d3704a73/mini.png)](https://insight.sensiolabs.com/projects/d73bade1-4158-4085-aab8-1042d3704a73)

This Symfony2 bundle provides a [Doctrine DBAL](https://github.com/doctrine/dbal) handler for [Monolog](https://github.com/Seldaek/monolog) and a web UI to display log entries. You can list, filter and paginate logs as you can see on the screenshot bellow:

![Log entries listing](https://github.com/lexik/LexikMonologBrowserBundle/raw/master/Resources/screen/list.jpg)
![Log entry show](https://github.com/lexik/LexikMonologBrowserBundle/raw/master/Resources/screen/show.jpg)

As this bundle query your database on each raised log, it's relevant for small and medium projects, but if you have billion of logs consider using a specific log server like [sentry](http://getsentry.com/), [airbrake](https://airbrake.io/), etc.

Requirements:
------------

* Symfony 2.1+
* KnpLabs/KnpPaginatorBundle

Installation
------------

Installation with composer:

``` json
    ...
    "require": {
        ...
        "lexik/monolog-browser-bundle": "~1.0",
        ...
    },
    ...
```

Next, be sure to enable these bundles in your `app/AppKernel.php` file:

``` php
public function registerBundles()
{
    return array(
        // ...
        new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        new Lexik\Bundle\MonologBrowserBundle\LexikMonologBrowserBundle(),
        // ...
    );
}
```

Configuration
-------------

First of all, you need to configure the Doctrine DBAL connection to use in the handler. You have 2 ways to do that:

**By using an existing Doctrine connection:**

Note: we set the `logging` and `profiling` option to false to avoid DI circular reference.

``` yaml
# app/config/config.yml
doctrine:
    dbal:
        connections:
            default:
                ...
            monolog:
                driver:    pdo_sqlite
                dbname:    monolog
                path:      %kernel.root_dir%/cache/monolog2.db
                charset:   UTF8
                logging:   false
                profiling: false

lexik_monolog_browser:
    doctrine:
        connection_name: monolog
```

**By creating a custom Doctrine connection for the bundle:**

``` yaml
# app/config/config.yml
lexik_monolog_browser:
    doctrine:
        connection:
            driver:      pdo_sqlite
            driverClass: ~
            pdo:         ~
            dbname:      monolog
            host:        localhost
            port:        ~
            user:        root
            password:    ~
            charset:     UTF8
            path:        %kernel.root_dir%/db/monolog.db # The filesystem path to the database file for SQLite
            memory:      ~                               # True if the SQLite database should be in-memory (non-persistent)
            unix_socket: ~                               # The unix socket to use for MySQL
```

Please refer to the [Doctrine DBAL connection configuration](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#configuration) for more details.

Optionally you can override the schema table name (`monolog_entries` by default):

``` yaml
# app/config/config.yml
lexik_monolog_browser:
    doctrine:
        table_name: monolog_entries
```

Now your database is configured, you can generate the schema for your log entry table by running the following command:

```
./app/console lexik:monolog-browser:schema-create
# you should see as result:
# Created table monolog_entries for Doctrine Monolog connection
```

Then, you can configure Monolog to use the Doctrine DBAL handler:

``` yaml
# app/config/config_prod.yml # or any env
monolog:
    handlers:
        main:
            type:         fingers_crossed # or buffer
            level:        error
            handler:      lexik_monolog_browser
        app:
            type:         buffer
            action_level: info
            channels:     app
            handler:      lexik_monolog_browser
        deprecation:
            type:         buffer
            action_level: warning
            channels:     deprecation
            handler:      lexik_monolog_browser
        lexik_monolog_browser:
            type:         service
            id:           lexik_monolog_browser.handler.doctrine_dbal
```

Now you have enabled and configured the handler, you migth want to display log entries, just import the routing file:

``` yaml
# app/config/routing.yml
lexik_monolog_browser:
    resource: "@LexikMonologBrowserBundle/Resources/config/routing.xml"
    prefix:   /admin/monolog
```

Translations
------------

If you wish to use default translations provided in this bundle, make sure you have enabled the translator in your config:

``` yaml
# app/config/config.yml
framework:
    translator: ~
```

Overriding default layout
-------------------------

You can override the default layout of the bundle by using the `base_layout` option:

``` yaml
# app/config/config.yml
lexik_monolog_browser:
    base_layout: "LexikMonologBrowserBundle::layout.html.twig"
```

or quite simply with the Symfony way by create a template on `app/Resources/LexikMonologBrowserBundle/views/layout.html.twig`.

Updating the bundle
-------------------

At each bundle updates, be careful to potential schema updates and because Monolog entries table is disconnected from the rest of your Doctrine entities or models, you have to manualy update the schema.

The bundle comes with a `schema-update` command but in some cases, like on renaming columns, the default behavior is not perfect and you may have a look to Doctrine Migrations (you can read an example on PR #2).

You can execute the command below to visualize SQL diff and execute schema updates:

```
./app/console lexik:monolog-browser:schema-update
```

ToDo
----

* configure Processors to push into the Handler
* abstract handler and connector for Doctrine and browse another like Elasticsearh
* write Tests
