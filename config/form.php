<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Zenstruck\RedirectBundle\Form\Type\RedirectType;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('zenstruck_redirect.redirect.form.type', RedirectType::class)
            ->args(['%zenstruck_redirect.redirect_class%'])
            ->tag('form.type', ['alias' => 'zenstruck_redirect']);
};
