<?php

namespace Zenstruck\RedirectBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Zenstruck\RedirectBundle\EventListener\NotFoundListener;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class NotFoundListenerTest extends TestCase
{
    /** @var NotFoundListener */
    protected $listener;

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

    protected function createEvent(\Exception $exception, Request $request = null, $requestType = HttpKernelInterface::MASTER_REQUEST)
    {
        return new ExceptionEvent(
            $this->createMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            $request ?: new Request(),
            $requestType,
            $exception
        );
    }
}
