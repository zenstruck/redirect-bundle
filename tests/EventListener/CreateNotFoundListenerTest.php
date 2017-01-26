<?php

namespace Zenstruck\RedirectBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\RedirectBundle\EventListener\CreateNotFoundListener;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class CreateNotFoundListenerTest extends NotFoundListenerTest
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $notFoundManager;

    public function setUp()
    {
        $this->notFoundManager = $this->getMock('Zenstruck\RedirectBundle\Service\NotFoundManager', array(), array(), '', false);
        $this->listener = new CreateNotFoundListener($this->notFoundManager);
    }

    public function testCreatesNotFound()
    {
        $request = Request::create('/foo/bar');

        $this->notFoundManager->expects($this->once())
            ->method('createFromRequest')
            ->with($request);

        $event = $this->createEvent(new NotFoundHttpException(), $request);

        $this->listener->onKernelException($event);
    }
}
