<?php

namespace Zenstruck\Bundle\RedirectBundle\Tests\Functional;

use Zenstruck\Bundle\RedirectBundle\Tests\Functional\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTest extends WebTestCase
{
    public function testRedirects()
    {
        $client = $this->createClient();

        $client->request('get', '/foo/bar');
        $this->assertRedirect($client->getResponse(), '/');
        $this->assertRedirectExists('/foo/bar', 301, 1);

        $client->request('get', '/foo/bar');
        $this->assertRedirect($client->getResponse(), '/');
        $this->assertRedirectExists('/foo/bar', 301, 2);
    }

    public function test404Logging()
    {
        $client = $this->createClient();

        $client->request('get', '/not-found');
        $this->assertRedirectExists('/not-found', 404, 1);

        $client->request('get', '/not-found');
        $this->assertRedirectExists('/not-found', 404, 2);
    }
}
