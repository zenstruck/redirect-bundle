<?php

namespace Zenstruck\Bundle\RedirectBundle\Tests\Entity;

use Zenstruck\Bundle\RedirectBundle\Entity\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $redirect = new Redirect();

        $this->assertEquals(0, $redirect->getCount());
        $this->assertEquals(301, $redirect->getStatusCode());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidStatusCode()
    {
        $redirect = new Redirect();

        $redirect->setStatusCode(666);
    }

    public function testSetStatusCode()
    {
        $redirect = new Redirect();

        $redirect->setStatusCode(302);
    }

}
