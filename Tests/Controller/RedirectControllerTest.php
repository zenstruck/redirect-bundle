<?php

namespace Zenstruck\Bundle\RedirectBundle\Tests\Controller;

use Zenstruck\Bundle\RedirectBundle\Tests\Functional\WebTestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectControllerTest extends WebTestCase
{
    public function testDoRedirect()
    {
        $client = $this->createClient();

        $client->request('get', '/redirect');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $client->request('get', '/redirect?url=%2Ffoo%23microsoft');
        $this->assertRedirect($client->getResponse(), 'http://www.microsoft.com');
        $this->assertRedirectExists('/foo#microsoft', 301, 1);
        $this->assertRedirectExists('/foo', 301, 0);

        $client->request('get', '/redirect?url=%2Ffoo%23microsoft');
        $this->assertRedirectExists('/foo#microsoft', 301, 2);

        $client->request('get', '/redirect?url=%2Ffoo%23google');
        $this->assertRedirect($client->getResponse(), 'http://www.google.com');
        $this->assertRedirectExists('/foo#google', 301, 1);

        $client->request('get', '/redirect?url=%2Ffoo');
        $this->assertRedirect($client->getResponse(), 'http://www.foo.com');
        $this->assertRedirectExists('/foo', 301, 1);
    }
}
