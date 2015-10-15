<?php

namespace Zenstruck\RedirectBundle\Tests\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class NotFoundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider pathProvider
     */
    public function testConstructor($path, $expectedPath)
    {
        $notFound = $this->createNotFound($path, 'http://foobar.com/baz');

        $this->assertSame($expectedPath, $notFound->getPath());
        $this->assertNull($notFound->getReferer());
        $this->assertSame('http://foobar.com/baz', $notFound->getFullUrl());
        $this->assertEquals(time(), $notFound->getTimestamp()->format('U'), '', 1);
    }

    public function pathProvider()
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
            array('foo/bar?baz=true', '/foo/bar'),
            array('http://www.example.com/foo?baz=bar&foo=baz', '/foo'),
            array('http://www.example.com/foo?baz=bar&foo=baz#baz', '/foo'),
            array('', null),
            array('   ', null),
            array('/', '/'),
        );
    }

    /**
     * @return \Zenstruck\RedirectBundle\Model\NotFound
     */
    private function createNotFound($path, $fullUrl, $referer = null, \DateTime $timestamp = null)
    {
        return $this->getMockForAbstractClass(
            'Zenstruck\RedirectBundle\Model\NotFound',
            array($path, $fullUrl, $referer, $timestamp)
        );
    }
}
