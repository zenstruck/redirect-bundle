**NOTE**: This bundle went under a major refactor 15-Oct-2012.  Use the `legacy` branch for the old version.

# Introduction

[![Build Status](https://secure.travis-ci.org/kbond/ZenstruckRedirectBundle.png)](http://travis-ci.org/kbond/ZenstruckRedirectBundle)

This bundle adds a database table that stores redirects for your site.  404 exceptions are intercepted and the requested
uri is looked up.  If a match is found it redirects to the found redirects destination.  The count and last accessed
date can be optionally stored as well.

In addition, 404 errors can be optionally logged as well.  Their count and last accessed date will also be stored.
This can be useful for determining bad links.

# Installation

1. Add `zenstruck/redirect-bundle` to your `composer.json` or this repository to your `deps` (if using Symfony 2.0)
2. Add the ``Zenstruck`` namespace to your ``app/autoloader.php`` (if not using composer)
3. Register the bundle (``new Zenstruck\Bundle\RedirectBundle\ZenstruckRedirectBundle()``)
4. (optional) add ``ZenstruckRedirectBundle`` to your doctrine mappings (not necessary if ``auto_mapping`` is true)
5. Create your redirect class inheriting the MappedSuperClass this bundle provides:

        namespace Acme\DemoBundle\Entity;

        use Zenstruck\Bundle\RedirectBundle\Entity\Redirect as BaseRedirect;
        use Doctrine\ORM\Mapping as ORM;

        /**
         * @ORM\Entity
         * @ORM\Table(name="redirect")
         */
        class Redirect extends BaseRedirect
        {
            /**
             * @ORM\Id
             * @ORM\Column(type="integer")
             * @ORM\GeneratedValue(strategy="AUTO")
             */
            protected $id;

        }

6. Set this class in your ``config.yml``:

        zenstruck_redirect:
            redirect_class: Acme\DemoBundle\Entity\Redirect

7. Update your schema (``doctrine:schema:update --force``)

# Configuration

By default the bundle simply intercepts your application's 404 errors and trys to find a matching entry in the database.

**Default configuration:**

    # app/config/config.yml
    ...

    zenstruck_redirect:
        redirect_class:         ~ # Required
        log_statistics:         false
        allow_404_query_params: false
    ...

* **``log_statistics``**: when enabled, the *count* and *last accessed* date for redirects are stored in the database.
Also, 404 errors will be logged
* **``allow_404_query_params``**: when enabled, 404 errors will be logged with a query string (`/foo/bar?baz=1` will be
logged as `/foo/bar?baz=1`).  By default, just the path is used (`/foo/bar?baz=1` is logged as `/foo/bar`)
