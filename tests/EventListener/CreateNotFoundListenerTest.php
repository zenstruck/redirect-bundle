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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\RedirectBundle\EventListener\CreateNotFoundListener;
use Zenstruck\RedirectBundle\Service\NotFoundManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CreateNotFoundListenerTest extends NotFoundListenerTestCase
{
    /** @var MockObject&NotFoundManager */
    private $notFoundManager;

    protected function setUp(): void
    {
        $this->notFoundManager = $this->createMock(NotFoundManager::class);
        $this->listener = new CreateNotFoundListener($this->notFoundManager);
    }

    /**
     * @test
     */
    public function creates_not_found(): void
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
