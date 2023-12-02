# ZenstruckRedirectBundle

[![CI](https://github.com/zenstruck/redirect-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/zenstruck/redirect-bundle/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/zenstruck/redirect-bundle.svg)](https://packagist.org/packages/zenstruck/redirect-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/zenstruck/redirect-bundle.svg)](https://packagist.org/packages/zenstruck/redirect-bundle)

This bundle adds entities for redirects and 404 errors.

For redirects, 404 errors are intercepted and the requested path is looked up. If a match is found it redirects to
the found redirect's destination. The count and last accessed date are updated as well. A redirect form type and
validation is available as well.

404 errors can be logged as well. Each 404 error is it's own record in the database. The path, full URL, timestamp, and
referer are stored. Storing each error as a separate record allows viewing statistics over time and seeing all the
referer URLs. When a redirect is created or updated, 404 records that match it's path are deleted.

## Installation

1. Install with composer:

        $ composer require zenstruck/redirect-bundle

2. Enable the bundle:
   This Step is only needed if you are not using [Symfony Flex](https://symfony.com/doc/current/setup/flex.html).

    ```php
    // config/bundles.php

    return [
            // ...
            Zenstruck\RedirectBundle\ZenstruckRedirectBundle::class => ['all' => true],
        ];
    ```

## Configuration

**NOTE:** A `NotFound` or `Redirect` or both must be configured.

### Redirect

1. Create your redirect class inheriting the MappedSuperClass this bundle provides:

    ```php
    namespace App\Entity;

    use Zenstruck\RedirectBundle\Model\Redirect as BaseRedirect;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="redirects")
     */
    class Redirect extends BaseRedirect
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;
    }
    ```

2. Set this class in your `zenstruck_redirect.yml`:

    ```yaml
    zenstruck_redirect:
        redirect_class: App\Entity\Redirect
    ```

3. Update your schema (or use a migration):

        $ bin/console doctrine:schema:update --force


### NotFound

1. Create your not found class inheriting the MappedSuperClass this bundle provides:

    ```php
    namespace App\Entity;

    use Zenstruck\RedirectBundle\Model\NotFound as BaseNotFound;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="not_founds")
     */
    class NotFound extends BaseNotFound
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;
    }
    ```

2. Set this class in your `zenstruck_redirect.yml`:

    ```yaml
    zenstruck_redirect:
        not_found_class: App\Entity\NotFound
    ```

3. Update your schema (or use a migration):

        $ bin/console doctrine:schema:update --force

## Form Type

This bundle provides a form type (`zenstruck_redirect`) for creating/editing redirects.

```php
$redirect = // ...
$form = $this->createForm('zenstruck_redirect', $redirect);
```

You may want to disable the `source` field for already created redirects:

```php
// new action
$redirect = new Redirect();
$form = $this->createForm('zenstruck_redirect', $redirect);

// edit action
$redirect = // get from database
$form = $this->createForm('zenstruck_redirect', $redirect, array('disable_source' => true));
```

## Full Default Configuration

```yaml
zenstruck_redirect:
    redirect_class:     ~ # Required if not_found_class is not set
    not_found_class:    ~ # Required if redirect_class is not set
    model_manager_name: ~

    # When enabled, when a redirect is updated or created, the NotFound entities with a matching path are removed.
    remove_not_founds: true
```
