<?php

namespace Zenstruck\RedirectBundle\Service;

use Doctrine\Persistence\ObjectManager;
use Zenstruck\RedirectBundle\Model\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectManager
{
    /**
     * @param string $class The Redirect class name
     */
    public function __construct(private string $class, private ObjectManager $om)
    {}

    public function findAndUpdate(string $source): ?Redirect
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
