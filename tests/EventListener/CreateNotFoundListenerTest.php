<?php

namespace Zenstruck\RedirectBundle\Tests\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\RedirectBundle\EventListener\CreateNotFoundListener;
use Zenstruck\RedirectBundle\Service\NotFoundManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class CreateNotFoundListenerTest extends NotFoundListenerTest
{
    /** @var MockObject&NotFoundManager $notFoundManager */
    private $notFoundManager;

    protected function setUp(): void
    {
        $this->notFoundManager = $this->createMock('Zenstruck\RedirectBundle\Service\NotFoundManager');
        $this->listener = new CreateNotFoundListener($this->notFoundManager);
    }

    /**
     * @test
     */
    public function creates_not_found()
    {
        $request = Request::create('/foo/bar');

        $this->notFoundManager->expects($this->once())
            ->method('createFromRequest')
            ->with($request)
        ;

        $event = $this->createEvent(new NotFoundHttpException(), $request);

        $this->listener->onKernelException($event);
    }
}
