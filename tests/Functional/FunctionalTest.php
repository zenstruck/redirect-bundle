<?php

namespace Zenstruck\RedirectBundle\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Zenstruck\RedirectBundle\Model\NotFound;
use Zenstruck\RedirectBundle\Model\Redirect;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyNotFound;
use Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity\DummyRedirect;
use Zenstruck\RedirectBundle\Tests\Fixture\TestKernel;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class FunctionalTest extends WebTestCase
{
    protected KernelBrowser $client;

    protected null|EntityManager $em;

    protected function setUp(): void
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

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected function getRedirect(string $source): ?Redirect
    {
        if (null === $redirect = $this->em->getRepository(DummyRedirect::class)->findOneBy(['source' => $source])) {
            return null;
        }

        $this->em->refresh($redirect);

        return $redirect;
    }

    /**
     * @return NotFound[]
     */
    protected function getNotFounds(): array
    {
        return $this->em->getRepository(DummyNotFound::class)->findAll();
    }

    protected function addTestData(): void
    {
        $this->em->createQuery('DELETE '.DummyRedirect::class)
            ->execute()
        ;

        $this->em->createQuery('DELETE '.DummyNotFound::class)
            ->execute()
        ;

        $this->em->persist(DummyRedirect::create('/301-redirect', 'http://symfony.com'));
        $this->em->persist(DummyRedirect::create('/302-redirect', 'http://example.com', false));

        $this->em->flush();
    }
}
