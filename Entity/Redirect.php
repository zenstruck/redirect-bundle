<?php

namespace Zenstruck\Bundle\RedirectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @ORM\Entity(repositoryClass="Zenstruck\Bundle\RedirectBundle\Repository\RedirectRepository")
 * @ORM\Table(name="redirect")
 * @ORM\HasLifeCycleCallbacks
 */
class Redirect
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $source;

    /**
     * @ORM\Column(type="string")
     */
    protected $destination;

    /**
     * @ORM\Column(type="smallint", name="status_code")
     */
    protected $statusCode;

    /**
     * @ORM\Column(type="integer")
     */
    protected $count;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="last_accessed")
     *
     * @var \DateTime $lastAccessed
     */
    protected $lastAccessed;

    public function __construct()
    {
        $this->statusCode = 301;
        $this->count = 0;
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
}