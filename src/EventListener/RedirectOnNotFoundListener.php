<?php

namespace Zenstruck\RedirectBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Zenstruck\RedirectBundle\Model\RedirectManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectOnNotFoundListener
{
    private $redirectManager;

    public function __construct(RedirectManager $redirectManager)
    {
        $this->redirectManager = $redirectManager;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $exception = $event->getException();

        if (!$exception instanceof HttpException || 404 !== (int) $exception->getStatusCode()) {
            return;
        }

        $redirect = $this->redirectManager->updateOrCreate($event->getRequest()->getPathInfo());

        if (null !== $destination = $redirect->getDestination()) {
            $event->setResponse(new RedirectResponse($destination, $redirect->getStatusCode()));
        }
    }
}
