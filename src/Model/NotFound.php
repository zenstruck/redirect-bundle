<?php

namespace Zenstruck\RedirectBundle\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class NotFound
{
    protected ?string $path = null;

    protected ?string $fullUrl = null;

    protected ?\DateTime $timestamp;

    protected ?string $referer;

    public static function create(string $path, string $fullUrl, ?string $referer = null, ?\DateTime $timestamp = null): static
    {
        $notFound = new static();

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

        $notFound->setPath($path);
        $notFound->setFullUrl($fullUrl);
        $notFound->setReferer($referer);
        $notFound->setTimestamp($timestamp);

        return $notFound;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getFullUrl(): ?string
    {
        return $this->fullUrl;
    }

    public function setFullUrl(?string $fullUrl): void
    {
        $this->fullUrl = $fullUrl;
    }

    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setReferer(?string $referer): void
    {
        $this->referer = $referer;
    }
}
