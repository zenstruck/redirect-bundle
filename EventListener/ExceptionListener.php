<?php

namespace Zenstruck\Bundle\RedirectBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Zenstruck\Bundle\RedirectBundle\Entity\RedirectManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExceptionListener
{
    protected $redirectManager;
    protected $logStatistics;
    protected $log404Errors;

    public function __construct(RedirectManager $redirectManager, $logStatistics = false, $log404Errors = false)
    {
        $this->redirectManager = $redirectManager;
        $this->logStatistics = $logStatistics;
        $this->log404Errors = $log404Errors;
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

        $redirect = $this->redirectManager->getRepository()->findOneBySource($source);

        if (!$redirect) {
            $class = $this->redirectManager->getClass();
            $redirect = new $class;
            $redirect->setSource($source);
        }

        // setup the response redirect if has destination
        if (!$redirect->is404Error()) {

            $destination = $redirect->getDestination();

            if (!$redirect->isDestinationAbsolute()) {
                $destination = $baseUrl . $destination;
            }

            $event->setResponse(new RedirectResponse($destination, $redirect->getStatusCode()));
        }

        $redirect->increaseCount();
        $redirect->setLastAccessed(new \DateTime());

        if (($this->logStatistics && !$redirect->is404Error()) || ($redirect->is404Error() && $this->log404Errors)) {
            $this->redirectManager->persistRedirect($redirect);
        }
    }

}
