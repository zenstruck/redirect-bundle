<?php

namespace Zenstruck\RedirectBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Zenstruck\RedirectBundle\Service\NotFoundManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class CreateNotFoundListener extends NotFoundListener
{
    private $notFoundManager;

    public function __construct(NotFoundManager $notFoundManager)
    {
        $this->notFoundManager = $notFoundManager;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->isNotFoundException($event)) {
            return;
        }

        $this->notFoundManager->createFromRequest($event->getRequest());
    }
}
