<?php

namespace Zenstruck\Bundle\RedirectBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Zenstruck\Bundle\RedirectBundle\Entity\RedirectManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExceptionListener
{
    /** @var RedirectManager **/
    protected $redirectManager;

    /** @var EngineInterface **/
    protected $templating;

    protected $template;
    protected $logStatistics;
    protected $log404Errors;

    public function __construct(RedirectManager $redirectManager, EngineInterface $templating, $template, $logStatistics = false, $log404Errors = false)
    {
        $this->redirectManager = $redirectManager;
        $this->templating = $templating;
        $this->template = $template;
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

        $response = $this->redirectManager->getResponse($request);

        if ($response) {
            $event->setResponse($response);
        }
    }

}
