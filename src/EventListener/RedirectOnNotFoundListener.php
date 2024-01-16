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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Zenstruck\RedirectBundle\Service\RedirectManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class RedirectOnNotFoundListener extends NotFoundListener
{
    public function __construct(private RedirectManager $redirectManager)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->isNotFoundException($event)) {
            return;
        }

        if (null === $redirect = $this->redirectManager->findAndUpdate($event->getRequest()->getPathInfo())) {
            return;
        }

        $event->setResponse(new RedirectResponse(
            $redirect->getDestination(),
            $redirect->isPermanent() ? 301 : 302,
        ));
    }
}
