<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
abstract class NotFoundListenerTestCase extends TestCase
{
    protected NotFoundListener $listener;

    /**
     * @test
     */
    public function non_master_request(): void
    {
        $event = $this->createEvent(new \Exception(), null, HttpKernelInterface::SUB_REQUEST);
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    /**
     * @test
     */
    public function non_http_exception(): void
    {
        $event = $this->createEvent(new \Exception());
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    /**
     * @test
     */
    public function non404_http_exception(): void
    {
        $event = $this->createEvent(new HttpException(403));
        $this->assertNull($event->getResponse());

        $this->listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    protected function createEvent(\Exception $exception, ?Request $request = null, $requestType = HttpKernelInterface::MAIN_REQUEST): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request ?: new Request(),
            $requestType,
            $exception
        );
    }
}
