<?php

/*
 * This file is part of the ZenstruckRedirectBundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Bundle\RedirectBundle\Tests\Entity;

use Zenstruck\Bundle\RedirectBundle\Tests\Fixtures\App\Bundle\Entity\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTest extends \PHPUnit_Framework_TestCase
{
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
        $this->assertEquals('/foo/bar', $redirect->getSource());
        $this->assertTrue($redirect->is404Error());

        $redirect->setSource('foo/bar#baz');
        $this->assertEquals('/foo/bar', $redirect->getSource());

        $redirect->setSource('foo/bar?neat=0#baz');
        $this->assertEquals('/foo/bar?neat=0', $redirect->getSource());

        $redirect->setSource('foo/bar?neat=0&baz=007#baz');
        $this->assertEquals('/foo/bar?neat=0&baz=007', $redirect->getSource());

        $redirect->setSource('foo/bar?foo=bar');
        $this->assertEquals('/foo/bar?foo=bar', $redirect->getSource());

        $redirect->setSource('foo/bar baz');
        $this->assertEquals('/foo/bar baz', $redirect->getSource());

        $redirect->setSource('foo/bar%20baz');
        $this->assertEquals('/foo/bar baz', $redirect->getSource());

        $redirect->setSource('/search?filter%5Bu%5D=33&referer=google');
        $this->assertEquals('/search?filter[u]=33&referer=google', $redirect->getSource());
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
        $this->assertFalse($redirect->is404Error());

        $redirect->setDestination('http://www.google.com');
        $this->assertEquals('http://www.google.com', $redirect->getDestination());

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

        $redirect->setStatusCode(301);

        // pre-persist
        $redirect->fixCodeForDestination();

        $this->assertEquals(404, $redirect->getStatusCode());

        $redirect->setDestination('http://www.google.com');

        // pre-persist
        $redirect->fixCodeForDestination();

        $this->assertEquals(301, $redirect->getStatusCode());
    }

}
