<?php

namespace Zenstruck\Bundle\RedirectBundle\Tests\Functional;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TestKernel extends Kernel
{
    function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),

            new \Zenstruck\Bundle\RedirectBundle\ZenstruckRedirectBundle(),

            new \Zenstruck\Bundle\RedirectBundle\Tests\Functional\Bundle\RedirectTestBundle()
        );
    }

    function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/default.yml');
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/'.Kernel::VERSION.'/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return sys_get_temp_dir().'/'.Kernel::VERSION.'/logs';
    }
}
