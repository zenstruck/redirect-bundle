<?php

namespace Zenstruck\RedirectBundle\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Redirect
{
    private $source;
    private $destination;
    private $statusCode;
    private $count = 0;
    private $lastAccessed;

    public function __construct($source, $destination = null, $statusCode = 404)
    {
        $this->setSource($source);
        $this->setDestination($destination);
        $this->setStatusCode($statusCode);
        $this->updateLastAccessed();
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $source = trim($source);
        $value = '/'.ltrim(parse_url($source, PHP_URL_PATH), '/');

        if ($query = parse_url($source, PHP_URL_QUERY)) {
            $value .= '?'.$query;
        }

        $this->source = $value;
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

        $this->destination = '' !== $destination ? $destination : null;
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
     * @return \DateTime
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
}
