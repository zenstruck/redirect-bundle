<?php

namespace Zenstruck\RedirectBundle\Tests\Functional;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class NotFoundTest extends FunctionalTest
{
    public function testNotFoundCreated()
    {
        $this->assertCount(0, $this->getNotFounds());

        $this->client->request('GET', '/not-found?foo=bar');

        $notFounds = $this->getNotFounds();

        $this->assertCount(1, $notFounds);
        $this->assertSame('/not-found', $notFounds[0]->getPath());
        $this->assertSame('http://localhost/not-found?foo=bar', $notFounds[0]->getFullUrl());
        $this->assertNull($notFounds[0]->getReferer());
    }
}
