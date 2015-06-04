<?php

namespace Zenstruck\RedirectBundle\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\RedirectBundle\Model\Redirect;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FunctionalTest extends WebTestCase
{
    /** @var Client */
    private $client;

    /** @var EntityManager */
    private $em;

    public function testRedirect()
    {
        $this->assertSame(0, $this->getRedirect('/redirect-to-symfony')->getCount());

        $this->client->followRedirects(false);
        $this->client->request('GET', '/redirect-to-symfony');
        $response = $this->client->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame('http://symfony.com', $response->getTargetUrl());
        $this->assertSame(1, $this->getRedirect('/redirect-to-symfony')->getCount());

        $this->client->request('GET', '/redirect-to-symfony?foo=bar');
        $response = $this->client->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame('http://symfony.com', $response->getTargetUrl());
        $this->assertSame(2, $this->getRedirect('/redirect-to-symfony')->getCount());
    }

    public function test404()
    {
        $this->assertNull($this->getRedirect('/foo'));

        try {
            $this->client->request('GET', '/foo');
        } catch (NotFoundHttpException $e) {
            $this->assertSame(1, $this->getRedirect('/foo')->getCount());

            try {
                $this->client->request('GET', '/foo?bar=baz');
            } catch (NotFoundHttpException $e) {
                $this->assertSame(2, $this->getRedirect('/foo')->getCount());

                return;
            }
        }

        $this->fail('NotFoundHttpException should have been thrown.');
    }

    public function setUp()
    {
        $client = self::createClient();

        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $application->run(new StringInput('doctrine:database:drop --force'), new NullOutput());
        $application->run(new StringInput('doctrine:database:create'), new NullOutput());
        $application->run(new StringInput('doctrine:schema:create'), new NullOutput());

        $this->em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->client = $client;

        $this->addTestData();
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

    private function addTestData()
    {
        $this->em->createQuery('DELETE TestBundle:DummyRedirect')
            ->execute()
        ;

        $this->em->persist(new DummyRedirect('/redirect-to-symfony', 'http://symfony.com'));
        $this->em->flush();
    }
}
