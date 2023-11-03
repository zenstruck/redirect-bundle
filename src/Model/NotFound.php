<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class NotFound
{
    protected ?string $path;

    protected ?\DateTime $timestamp;

    protected ?string $referer;

    public function __construct(?string $path, protected ?string $fullUrl, ?string $referer = null, ?\DateTime $timestamp = null)
    {
        if (null === $timestamp) {
            $timestamp = new \DateTime('now');
        }

        $path = \trim($path);
        $path = !empty($path) ? $path : null;

        if (null !== $path) {
            $parse_url = \parse_url($path, \PHP_URL_PATH);

            if (null != $parse_url) {
                $path = '/'.\ltrim($parse_url, '/');
            } else {
                $path = '/';
            }
        }

        $this->path = $path;
        $this->referer = $referer;
        $this->timestamp = $timestamp;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getFullUrl(): ?string
    {
        return $this->fullUrl;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }
}
