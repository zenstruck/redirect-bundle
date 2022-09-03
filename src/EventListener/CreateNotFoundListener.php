<?php

namespace Zenstruck\RedirectBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Zenstruck\RedirectBundle\Service\NotFoundManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class CreateNotFoundListener extends NotFoundListener
{
    public function __construct(private NotFoundManager $notFoundManager)
    {}

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->isNotFoundException($event)) {
            return;
        }

        $this->notFoundManager->createFromRequest($event->getRequest());
    }
}
