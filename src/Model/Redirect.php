<?php

namespace Zenstruck\RedirectBundle\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Redirect
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $destination;

    /**
     * @var bool
     */
    protected $permanent;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @var \DateTime
     */
    protected $lastAccessed = null;

    /**
     * @param string $source
     * @param string $destination
     * @param bool   $permanent
     */
    public function __construct($source, $destination, $permanent = true)
    {
        $this->setSource($source);
        $this->setDestination($destination);
        $this->setPermanent($permanent);
    }

    /**
     * @param string $destination
     * @param bool   $permanent
     *
     * @return static
     */
    public static function createFromNotFound(NotFound $notFound, $destination, $permanent = true)
    {
        return new static($notFound->getPath(), $destination, $permanent);
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $source = \trim($source);
        $source = !empty($source) ? $source : null;

        if (null !== $source) {
            $source = $this->createAbsoluteUri($source);
        }

        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     */
    public function setDestination($destination)
    {
        $destination = \trim($destination);
        $destination = !empty($destination) ? $destination : null;

        if (null !== $destination && null === \parse_url($destination, PHP_URL_SCHEME)) {
            $destination = $this->createAbsoluteUri($destination, true);
        }

        $this->destination = $destination;
    }

    /**
     * @return bool
     */
    public function isPermanent()
    {
        return $this->permanent;
    }

    /**
     * @param bool $permanent
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;
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
     * @param \DateTime $time
     */
    public function updateLastAccessed(\DateTime $time = null)
    {
        if (null === $time) {
            $time = new \DateTime('now');
        }

        $this->lastAccessed = $time;
    }

    /**
     * @param string $path
     * @param bool   $allowQueryString
     *
     * @return string
     */
    protected function createAbsoluteUri($path, $allowQueryString = false)
    {
        $value = '/'.\ltrim(\parse_url($path, PHP_URL_PATH), '/');

        if ($allowQueryString && $query = \parse_url($path, PHP_URL_QUERY)) {
            $value .= '?'.$query;
        }

        return $value;
    }
}
