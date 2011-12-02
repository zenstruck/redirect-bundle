<?php

/*
 * This file is part of the ZenstruckRedirectBundle package.
 *
 * (c) Kevin Bond <http://zenstruck.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Bundle\RedirectBundle\Entity;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Redirect
{
    const STATUS_CODE_NOTFOUND = 404;
    const STATUS_CODE_REDIRECT = 301;

    protected $source;

    protected $destination;

    protected $statusCode;

    protected $count;

    /**
     * @var \DateTime $lastAccessed
     */
    protected $lastAccessed;

    public function __construct()
    {
        $this->setStatusCode(self::STATUS_CODE_NOTFOUND);
        $this->setCount(0);
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set source
     *
     * @param string $source
     */
    public function setSource($source)
    {
        if ($source) {
            if ($this->isAbsolute($source)) {
                // remove host from url
                $source = explode(parse_url($source, PHP_URL_HOST), $source);

                $source = $source[1];
            }

            $source = $this->addSlashToURL($source);
         }

        $this->source = $source;
    }

    /**
     * Get source
     *
     * @return string $source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set destination
     *
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
     * Get destination
     *
     * @return string $destination
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
     * Set statusCode
     *
     * @param smallint $statusCode
     */
    public function setStatusCode($statusCode)
    {
        if (!in_array($statusCode, array_keys(Response::$statusTexts))) {
            throw new \InvalidArgumentException("Invalid status code");
        }

        $this->statusCode = $statusCode;
    }

    /**
     * Get statusCode
     *
     * @return smallint $statusCode
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set count
     *
     * @param integer $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * Get count
     *
     * @return integer $count
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
     * Set lastAccessed
     *
     * @param \DateTime $lastAccessed
     */
    public function setLastAccessed($lastAccessed)
    {
        $this->lastAccessed = $lastAccessed;
    }

    /**
     * Get lastAccessed
     *
     * @return \DateTime $lastAccessed
     */
    public function getLastAccessed()
    {
        return $this->lastAccessed;
    }

    public function fixCodeForEmptyDestination()
    {
        if (!$this->destination) {
            $this->setStatusCode(self::STATUS_CODE_NOTFOUND);
        } else {
            if ($this->statusCode === self::STATUS_CODE_NOTFOUND) {
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
        if (!preg_match('#^/#', $url)) {
            $url = '/' . $url;
        }

        return $url;
    }
}