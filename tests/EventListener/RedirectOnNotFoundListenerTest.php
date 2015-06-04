<?php

namespace Zenstruck\RedirectBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Zenstruck\RedirectBundle\EventListener\RedirectOnNotFoundListener;
use Zenstruck\RedirectBundle\Tests\Fixture\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectOnNotFoundListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $redirectManager;

    /** @var RedirectOnNotFoundListener */
    private $listener;

    public function setUp()
    {
        $this->redirectManager = $this->getMock('Zenstruck\RedirectBundle\Model\RedirectManager', array(), array(), '', false);
        $this->listener = new RedirectOnNotFoundListener($this->redirectManager);
    }

    public function testNonMasterRequest()
    {
        $event = $this->createEvent(new \Exception(), null, HttpKernelInterface::SUB_REQUEST);
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    public function testNonHttpException()
    {
        $event = $this->createEvent(new \Exception());
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    public function testNon404HttpException()
    {
        $event = $this->createEvent(new HttpException(403));
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    public function testHandles404()
    {
        $this->redirectManager->expects($this->once())
            ->method('updateOrCreate')
            ->with('/foo/bar')
            ->will($this->returnValue(new DummyRedirect('/foo/bar')));

        $event = $this->createEvent(new NotFoundHttpException(), Request::create('/foo/bar'));
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    public function testHandlesRedirect()
    {
        $this->redirectManager->expects($this->once())
            ->method('updateOrCreate')
            ->with('/foo/bar')
            ->will($this->returnValue(new DummyRedirect('/foo/bar', '/baz')));

        $event = $this->createEvent(new NotFoundHttpException(), Request::create('/foo/bar'));
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $response = $event->getResponse();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/baz', $response->getTargetUrl());
    }

    private function createEvent(\Exception $exception, Request $request = null, $requestType = HttpKernelInterface::MASTER_REQUEST)
    {
        return new GetResponseForExceptionEvent(
            $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            $request ?: new Request(),
            $requestType,
            $exception
        );
    }
}
