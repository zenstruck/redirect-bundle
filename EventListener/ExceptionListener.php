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

        $source = $request->getPathInfo();

        // if using dev env this will be set (ie /app_dev.php)
        $baseUrl = $request->getBaseUrl();

        $redirects = $this->redirectManager->findBySource($source);

        // more than 1 redirect found - render template to use javascript redirect
        if (count($redirects) > 1) {
            // json encode available sources
            $destinations = array();

            foreach ($redirects as $redirect) {
                $destinations[$redirect->getSource()] = $redirect->getDestination();
            }

            $event->setResponse($this->templating->renderResponse($this->template,
                    array(
                        'redirects' => $redirects,
                        'baseUrl'   => $baseUrl,
                        'sources'   => json_encode($destinations))));
            return;
        }

        $redirect = null;

        // only 1 redirect was found
        if (count($redirects)) {
            $redirect = $redirects[0];
        }

        // no redirect was found
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
