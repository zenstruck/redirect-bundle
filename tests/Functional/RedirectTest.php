<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\RedirectBundle\Tests\Fixture\Entity\DummyRedirect;

use function Zenstruck\Foundry\create;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RedirectTest extends KernelTestCase
{
    use Factories, HasBrowser, ResetDatabase;

    /**
     * @test
     */
    public function permanent_redirect(): void
    {
        $redirect = create(DummyRedirect::class, ['source' => '/301-redirect', 'destination' => '/']);
        $browser = $this->browser()->interceptRedirects();

        $this->assertSame(0, $redirect->getCount());

        $browser
            ->get('/301-redirect')
            ->assertStatus(301)
            ->assertRedirectedTo('/')
        ;
        $this->assertSame(1, $redirect->getCount());

        $browser
            ->get('/301-redirect')
            ->assertStatus(301)
            ->assertRedirectedTo('/')
        ;
        $this->assertSame(2, $redirect->getCount());
    }

    /**
     * @test
     */
    public function temporary_redirect(): void
    {
        $redirect = create(DummyRedirect::class, [
            'source' => '/302-redirect',
            'destination' => '/',
            'permanent' => false,
        ]);
        $browser = $this->browser()->interceptRedirects();

        $browser
            ->get('/302-redirect')
            ->assertStatus(302)
            ->assertRedirectedTo('/')
        ;
        $this->assertSame(1, $redirect->getCount());
    }
}
