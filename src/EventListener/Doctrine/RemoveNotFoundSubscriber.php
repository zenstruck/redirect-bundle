<?php

namespace Zenstruck\RedirectBundle\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zenstruck\RedirectBundle\Model\Redirect;
use Zenstruck\RedirectBundle\Service\NotFoundManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RemoveNotFoundSubscriber implements EventSubscriber
{
    private NotFoundManager|null $notFoundManager = null;

    public function __construct(private ContainerInterface $container)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            'postPersist',
            'postUpdate',
        ];
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->remoteNotFoundForRedirect($args);
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->remoteNotFoundForRedirect($args);
    }

    private function remoteNotFoundForRedirect(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Redirect) {
            return;
        }

        $this->getNotFoundManager()->removeForRedirect($entity);
    }

    private function getNotFoundManager(): NotFoundManager
    {
        if (null === $this->notFoundManager) {
            $this->notFoundManager = $this->container->get('zenstruck_redirect.not_found_manager');
        }

        return $this->notFoundManager;
    }
}
