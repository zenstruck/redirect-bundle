<?php

namespace Zenstruck\RedirectBundle\Tests\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider sourceProvider
     */
    public function testSetSource($source, $expected)
    {
        $redirect = $this->createRedirect($source);

        $this->assertSame($expected, $redirect->getSource());
    }

    /**
     * @dataProvider destinationProvider
     */
    public function testSetDestination($destination, $expected)
    {
        $redirect = $this->createRedirect('/', $destination);

        $this->assertSame($expected, $redirect->getDestination());
    }

    /**
     * @dataProvider statusCodeProvider
     */
    public function testSetStatusCode($statusCode, $expected)
    {
        $redirect = $this->createRedirect('/', null, $statusCode);

        $this->assertSame($expected, $redirect->getStatusCode());
    }

    public function sourceProvider()
    {
        return array(
            array('foo/bar', '/foo/bar'),
            array('/foo/bar/', '/foo/bar/'),
            array('foo', '/foo'),
            array('foo/bar ', '/foo/bar'),
            array(' foo/bar/', '/foo/bar/'),
            array('   /foo', '/foo'),
            array('http://www.example.com/foo', '/foo'),
            array('http://www.example.com/', '/'),
            array('http://www.example.com', '/'),
            array('foo/bar?baz=true', '/foo/bar?baz=true'),
            array('http://www.example.com/foo?baz=bar&foo=baz', '/foo?baz=bar&foo=baz'),
            array('http://www.example.com/foo?baz=bar&foo=baz#baz', '/foo?baz=bar&foo=baz'),
            array('', '/'),
            array('/', '/'),
        );
    }

    public function destinationProvider()
    {
        return array(
            array('/foo', '/foo'),
            array('foo', 'foo'),
            array(null, null),
            array('', null),
            array(' ', null),
            array('   ', null),
            array('http://www.example.com/foo', 'http://www.example.com/foo'),
        );
    }

    public function statusCodeProvider()
    {
        return array(
            array(200, 200),
            array('200', 200),
            array('', 0),
            array('  ', 0),
        );
    }

    /**
     * @return \Zenstruck\RedirectBundle\Model\Redirect
     */
    private function createRedirect($source, $destination = null, $statusCode = 404)
    {
        return $this->getMockForAbstractClass(
            'Zenstruck\RedirectBundle\Model\Redirect',
            array($source, $destination, $statusCode)
        );
    }
}
