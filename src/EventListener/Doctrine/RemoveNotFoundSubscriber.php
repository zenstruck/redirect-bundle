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
    private $container;

    private $notFoundManager;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postUpdate',
        ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->remoteNotFoundForRedirect($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->remoteNotFoundForRedirect($args);
    }

    private function remoteNotFoundForRedirect(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Redirect) {
            return;
        }

        $this->getNotFoundManager()->removeForRedirect($entity);
    }

    /**
     * @return NotFoundManager
     */
    private function getNotFoundManager()
    {
        if (null === $this->notFoundManager) {
            $this->notFoundManager = $this->container->get('zenstruck_redirect.not_found_manager');
        }

        return $this->notFoundManager;
    }
}
