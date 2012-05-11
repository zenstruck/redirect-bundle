<?php

namespace Zenstruck\Bundle\RedirectBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase as BaseWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Zenstruck\Bundle\RedirectBundle\Tests\Functional\Bundle\Entity\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class WebTestCase extends BaseWebTestCase
{
    protected $em;

    static public function assertRedirect($response, $location)
    {
        self::assertTrue($response->isRedirect(), 'Response is not a redirect, got status code: '.$response->getStatusCode());
        self::assertEquals($location, $response->headers->get('Location'));
    }

    public function assertRedirectExists($source, $statusCode = 301, $count = null)
    {
        /** @var $redirect Redirect */
        $redirect = $this->em->getRepository('RedirectTestBundle:Redirect')->findOneBySource($source);

        // redirect not current if not called - not sure why
        $this->em->refresh($redirect);

        $this->assertTrue((boolean) $redirect, sprintf('Redirect for "%s" not found', $source));
        $this->assertEquals($statusCode, $redirect->getStatusCode(), 'Status code wrong');

        if ($count) {
            $this->assertEquals($count, $redirect->getCount(), 'Count is wrong');
        }
    }

    static protected function getKernelClass()
    {
        require_once __DIR__.'/TestKernel.php';

        return 'Zenstruck\Bundle\RedirectBundle\Tests\Functional\TestKernel';
    }

    static protected function createKernel(array $options = array())
    {
        if (null === static::$class) {
            static::$class = static::getKernelClass();
        }

        return new static::$class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    public function setUp()
    {
        $client = $this->createClient();

        $application = new Application($client->getKernel());
        $application->setAutoExit(false);
        $this->runConsole($application, "doctrine:database:drop", array("--force" => true));
        $this->runConsole($application, "doctrine:database:create");
        $this->runConsole($application, "doctrine:schema:create");

        $this->em = $this->createClient()->getContainer()->get('doctrine')->getEntityManager();
        $this->addTestData();

        parent::setUp();
    }

    protected function runConsole($application, $command, Array $options = array())
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
        $redirects[1]->setSource('/foo#google');
        $redirects[1]->setDestination('http://www.google.com');

        $redirects[2] = new Redirect();
        $redirects[2]->setSource('/foo#microsoft');
        $redirects[2]->setDestination('http://www.microsoft.com');

        $redirects[3] = new Redirect();
        $redirects[3]->setSource('/foo');
        $redirects[3]->setDestination('http://www.foo.com');

        foreach ($redirects as $redirect) {
            $this->em->persist($redirect);
        }

        $this->em->flush();
    }
}
