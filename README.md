# Introduction

This bundle adds a database table that stores redirects for your site.  404 exceptions are intercepted and the requested uri is looked up.  If a match is found it redirects to the found redirects destination.  The count and last accessed date can be optionally stored as well.

In addition, 404 errors can be optionally logged as well.  Their count and last accessed date will also be stored.  This can be useful for determining a bad link.

# Installation

1. Install the bundle as normal (ie ``vendor/bundles/Zenstruck/Bundle/RedirectBundle``)
2. Add the ``Zenstruck`` namespace to your ``app/autoloader.php``
3. Register the bundle (``new Zenstruck/Bundle/RedirectBundle/ZenstruckRedirectBundle()``)
4. (optional) add ``ZenstruckRedirectBundle`` to your doctrine mappings (not necessary if ``auto_mapping`` is true)
5. Update your schema (``doctrine:schema:update --force``)

# Configuration

By default the bundle simply intercepts your application's 404 errors and trys to find a matching entry in the database.

**Default configuration**

    # app/config/config.yml
    ...

    zenstruck_redirect:
        log_statistics: false
        log_404_errors: false

    ...

* **``log_statistics``**: when enabled, the *count* and *last accessed* date for redirects are stored in the database.
* **``log_404_errors``**: when enabled, 404 errors are added to the database as redirects without destinations.  Their *count* and *last accessed* date are also stored.

# TODO

1. Create useful data accessor functions for the database
2. Create a GUI for reviewing/creating/editing redirects