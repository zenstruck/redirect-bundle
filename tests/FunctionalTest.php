<?php

namespace Zenstruck\RedirectBundle\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator;
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

    public function testValidation()
    {
        /** @var Validator $validator */
        $validator = self::$kernel->getContainer()->get('validator');

        $errors = $validator->validate(new DummyRedirect('/foo'));
        $this->assertCount(0, $errors);

        $errors = $validator->validate(new DummyRedirect('/301-redirect'));
        $this->assertCount(1, $errors);
        $this->assertSame('The source is already used', $errors[0]->getMessage());
        $this->assertSame('source', $errors[0]->getPropertyPath());

        $errors = $validator->validate(new DummyRedirect());
        $this->assertCount(1, $errors);
        $this->assertSame('Please enter a source', $errors[0]->getMessage());
        $this->assertSame('source', $errors[0]->getPropertyPath());

        $redirect = new DummyRedirect('/foo');
        $redirect->setStatusCode(500);
        $errors = $validator->validate($redirect);
        $this->assertCount(1, $errors);
        $this->assertSame('The status code is invalid', $errors[0]->getMessage());
        $this->assertSame('statusCode', $errors[0]->getPropertyPath());
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

        $this->em->persist(new DummyRedirect('/301-redirect', 'http://symfony.com'));

        $redirect = new DummyRedirect('/302-redirect', 'http://example.com');
        $redirect->setStatusCode(302);
        $this->em->persist($redirect);

        $this->em->flush();
    }
}
