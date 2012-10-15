<?php

/*
 * This file is part of the ZenstruckRedirectBundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Bundle\RedirectBundle\Entity;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Redirect
{
    const STATUS_CODE_NOTFOUND = 404;
    const STATUS_CODE_REDIRECT = 301;

    protected $source;

    protected $destination;

    protected $statusCode = self::STATUS_CODE_NOTFOUND;

    protected $count = 0;

    /**
     * @var \DateTime $lastAccessed
     */
    protected $lastAccessed;

    /**
     * @param $source
     */
    public function setSource($source)
    {
        $value = trim(parse_url($source, PHP_URL_PATH));

        if ($query = parse_url($source, PHP_URL_QUERY)) {
            $value .= '?' . $query;
        }

        $this->source = urldecode($this->addSlashToURL($value));
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        // see if absolute
        if ($this->isDestinationAbsolute()) {
            return;
        }

        if ($this->destination) {
            $this->destination = $this->addSlashToURL($this->destination);
        }
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Checks if destination is absolute (http://...) or not
     *
     * @return boolean
     */
    public function isDestinationAbsolute()
    {
        if (!$this->destination) {
            return false;
        }

        if ($this->isAbsolute($this->destination)) {
            return true;
        }

        return false;
    }

    public function is404Error()
    {
        return !((boolean) $this->destination);
    }

    /**
     * @param int $statusCode
     * @throws \InvalidArgumentException
     */
    public function setStatusCode($statusCode)
    {
        if (!in_array($statusCode, array_keys(Response::$statusTexts))) {
            throw new \InvalidArgumentException("Invalid status code");
        }

        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param integer $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Adds to count
     *
     * @param integer $amount
     */
    public function increaseCount($amount = 1)
    {
        $this->count += $amount;
    }

    /**
     * @param \DateTime $lastAccessed
     */
    public function setLastAccessed($lastAccessed)
    {
        $this->lastAccessed = $lastAccessed;
    }

    /**
     * @return \DateTime
     */
    public function getLastAccessed()
    {
        return $this->lastAccessed;
    }

    public function fixCodeForDestination()
    {
        if (!$this->destination) {
            $this->setStatusCode(self::STATUS_CODE_NOTFOUND);
        } else {
            // make sure not a 404 error
            if (self::STATUS_CODE_NOTFOUND === $this->statusCode) {
                $this->setStatusCode(self::STATUS_CODE_REDIRECT);
            }
        }
    }

    protected function isAbsolute($path)
    {
        if (preg_match('#^\w+://#', $path)) {
            return true;
        }

        return false;
    }

    protected function addSlashToURL($url)
    {
        return sprintf('/%s', trim($url, '/'));
    }
}