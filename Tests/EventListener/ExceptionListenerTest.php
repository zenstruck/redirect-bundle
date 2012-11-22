<?php

namespace Zenstruck\Bundle\RedirectBundle\Tests\EventListener;

use Zenstruck\Bundle\RedirectBundle\Tests\Functional\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExceptionListenerTest extends WebTestCase
{
    public function testDefaultRedirects()
    {
        $client = $this->prepareEnvironment();

        $client->request('get', '/foo/bar');
        $this->assertRedirect($client->getResponse(), '/');
        $this->assertRedirectExists('/foo/bar', 301, 0);

        $client->request('get', '/foo/bar');
        $this->assertRedirect($client->getResponse(), '/');
        $this->assertRedirectExists('/foo/bar', 301, 0);

        $client->request('get', '/foo/google');
        $this->assertRedirect($client->getResponse(), 'http://www.google.com');
        $this->assertRedirectExists('/foo/google', 301, 0);

        $client->request('get', '/foo?bar=10');
        $this->assertRedirect($client->getResponse(), 'http://www.bar.com');
        $this->assertRedirectExists('/foo?bar=10', 301, 0);
    }

    public function testDefault404()
    {
        $client = $this->prepareEnvironment();

        try {
            $client->request('get', '/not-found');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectDoesNotExist('/not-found');
    }

    public function testStatsEnabledRedirects()
    {
        $client = $this->prepareEnvironment('stats');

        $client->request('get', '/foo/bar');
        $this->assertRedirect($client->getResponse(), '/');
        $this->assertRedirectExists('/foo/bar', 301, 1);

        $client->request('get', '/foo/bar');
        $this->assertRedirect($client->getResponse(), '/');
        $this->assertRedirectExists('/foo/bar', 301, 2);

        $client->request('get', '/foo?bar=10');
        $this->assertRedirect($client->getResponse(), 'http://www.bar.com');
        $this->assertRedirectExists('/foo?bar=10', 301, 1);

        $client->request('get', '/foo?bar=10');
        $this->assertRedirect($client->getResponse(), 'http://www.bar.com');
        $this->assertRedirectExists('/foo?bar=10', 301, 2);

        $client->request('get', '/no/path/version?foo=bar');
        $this->assertRedirectExists('/no/path/version?foo=bar', 301, 1);
    }

    public function testStatsEnabled404()
    {
        $client = $this->prepareEnvironment('stats');

        try {
            $client->request('get', '/not-found');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/not-found', 404, 1);

        try {
            $client->request('get', '/not-found');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/not-found', 404, 2);

        try {
            $client->request('get', '/not-found?foo=bar');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/not-found', 404, 3);

        try {
            $client->request('get', '/no/path/version');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/no/path/version', 404, 1);

        try {
            $client->request('get', '/no/path/version?foo=none');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/no/path/version', 404, 2);
    }

    public function testAllowQueryParams()
    {
        $client = $this->prepareEnvironment('allow_query_params');

        try {
            $client->request('get', '/not-found');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/not-found', 404, 1);

        try {
            $client->request('get', '/not-found');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/not-found', 404, 2);

        try {
            $client->request('get', '/not-found?foo=bar');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/not-found?foo=bar', 404, 1);

        try {
            $client->request('get', '/no/path/version');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/no/path/version', 404, 1);

        try {
            $client->request('get', '/no/path/version?foo=none');
        } catch (NotFoundHttpException $e) {}

        $this->assertRedirectExists('/no/path/version?foo=none', 404, 1);
    }
}
