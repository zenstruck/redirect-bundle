<?php

namespace Zenstruck\Bundle\RedirectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Zenstruck\Bundle\RedirectBundle\Entity\Redirect;

class FixtureLoader implements FixtureInterface
{

    public function load($manager)
    {
        $redirect1 = new Redirect();
        $redirect1->setSource('foo/bar');
        $redirect1->setDestination('/');
        $redirect1->setStatusCode(302);
        $manager->persist($redirect1);

        $redirect2 = new Redirect();
        $redirect2->setSource('foo/baz');
        $redirect2->setDestination('http://www.google.com/');
        $manager->persist($redirect2);

        $notfound1 = new Redirect();
        $notfound1->setSource('not/found');
        $manager->persist($notfound1);

        $manager->flush();
    }

}
