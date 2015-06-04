<?php

namespace Zenstruck\RedirectBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectManager
{
    private $class;
    private $om;

    public function __construct($class, ObjectManager $om)
    {
        $this->class = $class;
        $this->om = $om;
    }

    /**
     * @param string $source
     *
     * @return Redirect
     */
    public function updateOrCreate($source)
    {
        /** @var Redirect|null $redirect */
        $redirect = $this->om->getRepository($this->class)->findOneBy(array('source' => $source));

        if (null === $redirect) {
            $redirect = new $this->class($source);
            $this->om->persist($redirect);
        }

        $redirect->increaseCount();
        $redirect->updateLastAccessed();

        $this->om->flush();

        return $redirect;
    }
}
