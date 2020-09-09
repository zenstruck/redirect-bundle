<?php

namespace Zenstruck\RedirectBundle\Service;

use Doctrine\Persistence\ObjectManager;
use Zenstruck\RedirectBundle\Model\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectManager
{
    private $class;

    private $om;

    /**
     * @param string $class The Redirect class name
     */
    public function __construct($class, ObjectManager $om)
    {
        $this->class = $class;
        $this->om = $om;
    }

    /**
     * @param string $source
     *
     * @return Redirect|null
     */
    public function findAndUpdate($source)
    {
        /** @var Redirect|null $redirect */
        $redirect = $this->om->getRepository($this->class)->findOneBy(['source' => $source]);

        if (null === $redirect) {
            return null;
        }

        $redirect->increaseCount();
        $redirect->updateLastAccessed();
        $this->om->flush();

        return $redirect;
    }
}
