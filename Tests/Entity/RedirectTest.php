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

        $this->assertEquals(302, $redirect->getStatusCode());
    }

    public function testSetSource()
    {
        $redirect = new Redirect();

        $redirect->setSource('/foo/bar');

        $this->assertEquals('/foo/bar', $redirect->getSource());

        $redirect->setSource('foo/bar');

        $this->assertEquals('/foo/bar', $redirect->getSource());

        $redirect->setSource('/foo/bar#test');

        $this->assertEquals('/foo/bar', $redirect->getSource());

        $redirect->setSource('http://www.google.com/foo/bar#test');

        $this->assertEquals('/foo/bar', $redirect->getSource());

        $this->assertTrue($redirect->is404Error() === true);
    }

    public function testSetDestination()
    {
        $redirect = new Redirect();

        $redirect->setDestination('/foo/bar');

        $this->assertEquals('/foo/bar', $redirect->getDestination());

        $redirect->setDestination('foo/bar');

        $this->assertEquals('/foo/bar', $redirect->getDestination());

        $redirect->setDestination('http://www.google.com/');

        $this->assertEquals('http://www.google.com/', $redirect->getDestination());

        $this->assertTrue($redirect->is404Error() === false);
    }

    public function testPrePersist()
    {
        $redirect = new Redirect();

        $redirect->fixCodeForEmptyDestination();

        $this->assertEquals(404, $redirect->getStatusCode());
    }

}
