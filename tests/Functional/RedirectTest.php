<?php

namespace Zenstruck\RedirectBundle\Tests\Functional;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTest extends FunctionalTest
{
    /**
     * @test
     */
    public function test301_redirect()
    {
        $this->assertSame(0, $this->getRedirect('/301-redirect')->getCount());

        $this->client->followRedirects(false);
        $this->client->request('GET', '/301-redirect');
        $response = $this->client->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame('http://symfony.com', $response->getTargetUrl());
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame(1, $this->getRedirect('/301-redirect')->getCount());

        $this->client->request('GET', '/301-redirect?foo=bar');
        $response = $this->client->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame('http://symfony.com', $response->getTargetUrl());
        $this->assertSame(2, $this->getRedirect('/301-redirect')->getCount());
    }

    /**
     * @test
     */
    public function test302_redirect()
    {
        $this->assertSame(0, $this->getRedirect('/302-redirect')->getCount());

        $this->client->followRedirects(false);
        $this->client->request('GET', '/302-redirect');
        $response = $this->client->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame('http://example.com', $response->getTargetUrl());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(1, $this->getRedirect('/302-redirect')->getCount());
    }
}
