# ZenstruckRedirectBundle

[![Build Status](http://img.shields.io/travis/kbond/ZenstruckRedirectBundle.svg?style=flat-square)](https://travis-ci.org/kbond/ZenstruckRedirectBundle)
[![Latest Stable Version](http://img.shields.io/packagist/v/zenstruck/redirect-bundle.svg?style=flat-square)](https://packagist.org/packages/zenstruck/redirect-bundle)
[![License](http://img.shields.io/packagist/l/zenstruck/redirect-bundle.svg?style=flat-square)](https://packagist.org/packages/zenstruck/redirect-bundle)

This bundle adds a database table that stores redirects for your site. 404 exceptions are intercepted and the requested
uri is looked up. If a match is found it redirects to the found redirects destination. The count and last accessed
date are stored as well.

In addition, 404 errors are logged as well. Their count and last accessed date will also be stored. This can be useful
for determining bad links.

## Installation

1. Install with composer:

        $ composer require zenstruck/redirect-bundle

2. Enable the bundle in the kernel:

    ```php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Zenstruck\RedirectBundle\ZenstruckRedirectBundle(),
        );
    }
    ```

3. Create your redirect class inheriting the MappedSuperClass this bundle provides:

    ```php
    namespace Acme\DemoBundle\Entity;

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

4. Set this class in your `config.yml`:

    ```yaml
    zenstruck_redirect:
        redirect_class: Acme\DemoBundle\Entity\Redirect
    ```

7. Update your schema:

        $ app/console doctrine:schema:update --force

# Form Type

This bundle provides a form type (`zenstruck_redirect`) for creating/editing redirects.

```php
$redirect = // ...
$form = $this->createForm('zenstruck_redirect', $redirect);
```

By default, a redirect with a `destination` has a status code of `301`, to give the option to set a `302` status code
a status code choice field can be enabled when creating the form:

```php
$redirect = // ...
$form = $this->createForm('zenstruck_redirect', $redirect, array('status_code' => true));
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
    redirect_class:     ~ # Required
    model_manager_name: ~
```
