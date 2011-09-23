<?php

namespace Zenstruck\Bundle\RedirectBundle\Entity;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Redirect
{
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
        $this->setStatusCode(301);
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
        $source = parse_url($source, PHP_URL_PATH);

        $source = $this->addSlashToURL($source);

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

        $this->destination = $this->addSlashToURL($this->destination);
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
            $this->setStatusCode(404);
        }
    }

    protected function isAbsolute($path)
    {
        if (preg_match('#^\w+://#', $this->destination)) {
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