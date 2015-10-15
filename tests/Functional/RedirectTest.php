<?php

namespace Zenstruck\RedirectBundle\Tests\Functional;

use Zenstruck\RedirectBundle\Model\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTest extends FunctionalTest
{
    public function test301Redirect()
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

    public function test302Redirect()
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

    /**
     * @param string $source
     *
     * @return Redirect|null
     */
    private function getRedirect($source)
    {
        if (null === $redirect = $this->em->getRepository('TestBundle:DummyRedirect')->findOneBy(array('source' => $source))) {
            return null;
        }

        $this->em->refresh($redirect);

        return $redirect;
    }
}
