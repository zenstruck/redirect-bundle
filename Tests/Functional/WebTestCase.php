<?php

namespace Zenstruck\Bundle\RedirectBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Zenstruck\Bundle\RedirectBundle\Tests\Functional\Bundle\Entity\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class WebTestCase extends BaseWebTestCase
{
    /** @var $em \Doctrine\ORM\EntityManager */
    protected $em;

    public function assertRedirect(Response $response, $location)
    {
        self::assertThat($response->isRedirect(), self::isTrue(), 'Response is not a redirect, got status code: '.$response->getStatusCode());
        self::assertThat($location === $response->headers->get('Location'), self::isTrue());
    }

    public function assertRedirectExists($source, $statusCode = 301, $count = null)
    {
        /** @var $redirect Redirect */
        $redirect = $this->em->getRepository('RedirectTestBundle:Redirect')->findOneBySource($source);

        // redirect not current if not called - not sure why
        if ($redirect) {
            $this->em->refresh($redirect);
        }

        self::assertThat((boolean) $redirect, self::isTrue(), sprintf('Redirect for "%s" not found', $source));
        self::assertThat($statusCode === $redirect->getStatusCode(), self::isTrue(), 'Status code wrong');

        if ($count) {
            self::assertThat($count === $redirect->getCount(), self::isTrue(), 'Count is wrong');
        }
    }

    public function assertRedirectDoesNotExist($source)
    {
        /** @var $redirect Redirect */
        $redirect = $this->em->getRepository('RedirectTestBundle:Redirect')->findOneBySource($source);

        // redirect not current if not called - not sure why
        if ($redirect) {
            $this->em->refresh($redirect);
        }

        self::assertThat((boolean) $redirect, self::isFalse());
    }

    static protected function getKernelClass()
    {
        return 'Zenstruck\Bundle\RedirectBundle\Tests\Functional\TestKernel';
    }

    public function getClient($environment = 'default')
    {
        $client = parent::createClient(array('environment' => $environment));

        $application = new Application($client->getKernel());
        $application->setAutoExit(false);
        $this->runConsole($application, "doctrine:database:drop", array("--force" => true));
        $this->runConsole($application, "doctrine:database:create");
        $this->runConsole($application, "doctrine:schema:create");

        $this->em = $client->getContainer()->get('doctrine')->getEntityManager();
        $this->addTestData();

        return $client;
    }

    protected function runConsole($application, $command, array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }

    protected function addTestData()
    {
        // empty db
        $this->em->createQuery('DELETE RedirectTestBundle:Redirect')
            ->execute()
        ;

        $redirects = array();

        $redirects[0] = new Redirect();
        $redirects[0]->setSource('/foo/bar');
        $redirects[0]->setDestination('/');

        $redirects[1] = new Redirect();
        $redirects[1]->setSource('/foo/google');
        $redirects[1]->setDestination('http://www.google.com');

        $redirects[2] = new Redirect();
        $redirects[2]->setSource('/foo/microsoft');
        $redirects[2]->setDestination('http://www.microsoft.com');

        $redirects[3] = new Redirect();
        $redirects[3]->setSource('/foo');
        $redirects[3]->setDestination('http://www.foo.com');

        $redirects[4] = new Redirect();
        $redirects[4]->setSource('/foo?bar=10');
        $redirects[4]->setDestination('http://www.bar.com');

        $redirects[5] = new Redirect();
        $redirects[5]->setSource('/foo?baz=20');
        $redirects[5]->setDestination('http://www.baz.com');

        $redirects[6] = new Redirect();
        $redirects[6]->setSource('/no/path/version?foo=bar');
        $redirects[6]->setDestination('http://www.nopath.com');

        foreach ($redirects as $redirect) {
            $this->em->persist($redirect);
        }

        $this->em->flush();
    }
}
