<?php

namespace Zenstruck\RedirectBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class NotFoundListener
{
    public function isNotFoundException(ExceptionEvent $event): bool
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            return false;
        }

        $exception = $event->getThrowable();

        if (!$exception instanceof HttpException || 404 !== $exception->getStatusCode()) {
            return false;
        }

        return true;
    }

    abstract public function onKernelException(ExceptionEvent $event);
}
