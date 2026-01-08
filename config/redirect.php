<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Zenstruck\RedirectBundle\EventListener\RedirectOnNotFoundListener;
use Zenstruck\RedirectBundle\Service\RedirectManager;

return static function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('zenstruck_redirect.redirect_manager.class', RedirectManager::class)
        ->set('zenstruck_redirect.redirect_listener.class', RedirectOnNotFoundListener::class)
    ;

    $container->services()
        ->set('zenstruck_redirect.redirect_manager', '%zenstruck_redirect.redirect_manager.class%')
            ->args([
                '%zenstruck_redirect.redirect_class%',
                service('zenstruck_redirect.entity_manager'),
            ])
        ->set('zenstruck_redirect.redirect_listener', '%zenstruck_redirect.redirect_listener.class%')
            ->args([service('zenstruck_redirect.redirect_manager')])
            ->tag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onKernelException', 'priority' => 10])
    ;
};
