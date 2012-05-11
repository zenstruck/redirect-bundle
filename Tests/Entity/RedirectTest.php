<?php

/*
 * This file is part of the ZenstruckRedirectBundle package.
 *
 * (c) Kevin Bond <http://zenstruck.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $this->assertEquals(404, $redirect->getStatusCode());
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

        $redirect->setSource('http://www.google.com/foo/bar#test');
        $this->assertEquals('/foo/bar#test', $redirect->getSource());
        $this->assertTrue($redirect->is404Error() === true);

        $redirect->setSource('foo/bar#baz');
        $this->assertEquals('/foo/bar#baz', $redirect->getSource());

        $redirect->setSource('foo/bar?foo=bar');
        $this->assertEquals('/foo/bar?foo=bar', $redirect->getSource());

        $redirect->setSource('foo/bar baz');
        $this->assertEquals('/foo/bar baz', $redirect->getSource());

        $redirect->setSource('foo/bar%20baz');
        $this->assertEquals('/foo/bar baz', $redirect->getSource());
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

        $redirect->setDestination('foo/bar#baz');
        $this->assertEquals('/foo/bar#baz', $redirect->getDestination());

        $redirect->setDestination('foo/bar?foo=bar');
        $this->assertEquals('/foo/bar?foo=bar', $redirect->getDestination());

        $redirect->setDestination('foo/bar?foo=bar#baz');
        $this->assertEquals('/foo/bar?foo=bar#baz', $redirect->getDestination());
    }

    public function testPrePersist()
    {
        $redirect = new Redirect();

        $redirect->fixCodeForEmptyDestination();

        $this->assertEquals(404, $redirect->getStatusCode());
    }

}
