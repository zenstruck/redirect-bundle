<?php

/*
 * This file is part of the ZenstruckRedirectBundle package.
 *
 * (c) Kevin Bond <http://zenstruck.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Bundle\RedirectBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\Bundle\RedirectBundle\Entity\RedirectManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExceptionListener
{
    /** @var RedirectManager **/

    public function __construct(RedirectManager $redirectManager)
    {
        $this->redirectManager = $redirectManager;
    }

    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        // only catch 404 exceptions
        if (!($exception instanceof NotFoundHttpException)) {
            return;
        }

        $source = $request->getPathInfo();

        // if using dev env this will be set (ie /app_dev.php)
        $baseUrl = $request->getBaseUrl();

        $response = $this->redirectManager->getResponse($source, $baseUrl);

        if ($response) {
            $event->setResponse($response);
        }
    }

}
