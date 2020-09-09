<?php

namespace Zenstruck\RedirectBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\RedirectBundle\EventListener\RedirectOnNotFoundListener;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectOnNotFoundListenerTest extends NotFoundListenerTest
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $redirectManager;

    public function setUp(): void
    {
        $this->redirectManager = $this->createMock('Zenstruck\RedirectBundle\Service\RedirectManager', [], [], '', false);
        $this->listener = new RedirectOnNotFoundListener($this->redirectManager);
    }

    /**
     * @test
     */
    public function handle_redirect()
    {
        $this->redirectManager->expects($this->once())
            ->method('findAndUpdate')
            ->with('/foo/bar')
            ->willReturn(new DummyRedirect('/foo/bar', '/baz'))
        ;

        $event = $this->createEvent(new NotFoundHttpException(), Request::create('/foo/bar'));
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $response = $event->getResponse();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/baz', $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function cannot_handle_redirect()
    {
        $this->redirectManager->expects($this->once())
            ->method('findAndUpdate')
            ->with('/foo/bar')
            ->willReturn(null)
        ;

        $event = $this->createEvent(new NotFoundHttpException(), Request::create('/foo/bar'));
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }
}
