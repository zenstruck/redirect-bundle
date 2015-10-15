<?php

namespace Zenstruck\RedirectBundle\Tests\Functional;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\RedirectBundle\Model\NotFound;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class NotFoundTest extends FunctionalTest
{
    public function testNotFoundCreated()
    {
        $this->assertCount(0, $this->getNotFounds());

        try {
            $this->client->request('GET', '/not-found?foo=bar');
        } catch (NotFoundHttpException $e) {
            $notFounds = $this->getNotFounds();

            $this->assertCount(1, $notFounds);
            $this->assertSame('/not-found', $notFounds[0]->getPath());
            $this->assertSame('http://localhost/not-found?foo=bar', $notFounds[0]->getFullUrl());
            $this->assertNull($notFounds[0]->getReferer());

            return;
        }

        $this->fail('NotFoundHttpException should have been thrown.');
    }

    /**
     * @return NotFound[]
     */
    private function getNotFounds()
    {
        return $this->em->getRepository('TestBundle:DummyNotFound')->findAll();
    }
}
