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

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\RedirectBundle\EventListener\RedirectOnNotFoundListener;
use Zenstruck\RedirectBundle\Service\RedirectManager;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RedirectOnNotFoundListenerTest extends NotFoundListenerTestCase
{
    /** @var MockObject&RedirectManager */
    private $redirectManager;

    protected function setUp(): void
    {
        $this->redirectManager = $this->createMock(RedirectManager::class);
        $this->listener = new RedirectOnNotFoundListener($this->redirectManager);
    }

    /**
     * @test
     */
    public function handle_redirect(): void
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
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/baz', $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function cannot_handle_redirect(): void
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
