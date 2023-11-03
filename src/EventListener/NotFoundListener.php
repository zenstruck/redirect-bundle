<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
abstract class NotFoundListener
{
    abstract public function onKernelException(ExceptionEvent $event);

    final protected function isNotFoundException(ExceptionEvent $event): bool
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            return false;
        }

        $exception = $event->getThrowable();

        return $exception instanceof HttpException && 404 === $exception->getStatusCode();
    }
}
