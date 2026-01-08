<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Zenstruck\RedirectBundle\EventListener\Doctrine\RemoveNotFoundSubscriber;

return static function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('zenstruck_redirect.remove_not_found_subscriber.class', RemoveNotFoundSubscriber::class);

    $container->services()
        ->set('zenstruck_redirect.remove_not_found_subscriber', '%zenstruck_redirect.remove_not_found_subscriber.class%')
            ->args([service('service_container')])
            ->tag('doctrine.event_listener', ['event' => 'postPersist'])
            ->tag('doctrine.event_listener', ['event' => 'postUpdate']);
};
