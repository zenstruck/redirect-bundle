<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Zenstruck\RedirectBundle\EventListener\CreateNotFoundListener;
use Zenstruck\RedirectBundle\Service\NotFoundManager;

return static function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('zenstruck_redirect.not_found_manager.class', NotFoundManager::class)
        ->set('zenstruck_redirect.not_found_listener.class', CreateNotFoundListener::class)
    ;

    $container->services()
        ->set('zenstruck_redirect.not_found_manager', '%zenstruck_redirect.not_found_manager.class%')
            ->public()
            ->args([
                '%zenstruck_redirect.not_found_class%',
                service('zenstruck_redirect.entity_manager'),
            ])
        ->set('zenstruck_redirect.not_found_listener', '%zenstruck_redirect.not_found_listener.class%')
            ->args([service('zenstruck_redirect.not_found_manager')])
            ->tag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onKernelException'])
    ;
};
