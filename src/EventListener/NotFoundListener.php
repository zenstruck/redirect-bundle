<?php

namespace Zenstruck\RedirectBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class NotFoundListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @return bool
     */
    public function isNotFoundException(GetResponseForExceptionEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return false;
        }

        $exception = method_exists($event, 'getThrowable') ? $event->getThrowable() : $event->getException();

        if (!$exception instanceof HttpException || 404 !== (int) $exception->getStatusCode()) {
            return false;
        }

        return true;
    }

    abstract public function onKernelException(GetResponseForExceptionEvent $event);
}
