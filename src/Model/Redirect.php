<?php

namespace Zenstruck\RedirectBundle\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Redirect
{
    private $source;
    private $destination;
    private $statusCode = 404;
    private $count = 0;
    private $lastAccessed;

    public function __construct($source = null, $destination = null)
    {
        $this->setSource($source);
        $this->setDestination($destination);
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $source = trim($source);
        $source = !empty($source) ? $source : null;

        if (null === $source) {
            $this->source = $source;

            return;
        }

        $this->source = $this->createAbsoluteUri($source);
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string|null $destination
     */
    public function setDestination($destination)
    {
        $destination = trim($destination);
        $destination = !empty($destination) ? $destination : null;

        if (null === $destination) {
            $this->setStatusCode(404);
            $this->destination = null;

            return;
        }

        if (404 === $this->getStatusCode()) {
            $this->setStatusCode(301);
        }

        if (null !== parse_url($destination, PHP_URL_SCHEME)) {
            // absolute url
            $this->destination = $destination;

            return;
        }

        $this->destination = $this->createAbsoluteUri($destination);
    }

    /**
     * @return string|null
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (int) $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $amount
     */
    public function increaseCount($amount = 1)
    {
        $this->count += $amount;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastAccessed()
    {
        return $this->lastAccessed;
    }

    /**
     * Set lastAccessed to the current time.
     */
    public function updateLastAccessed()
    {
        $this->lastAccessed = new \DateTime('now');
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function createAbsoluteUri($path)
    {
        $value = '/'.ltrim(parse_url($path, PHP_URL_PATH), '/');

        if ($query = parse_url($path, PHP_URL_QUERY)) {
            $value .= '?'.$query;
        }

        return $value;
    }
}
