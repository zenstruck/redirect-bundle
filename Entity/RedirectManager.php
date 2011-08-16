<?php

namespace Zenstruck\Bundle\RedirectBundle\Entity;

use Doctrine\ORM\EntityManager;
use Zenstruck\Bundle\RedirectBundle\Entity\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectManager
{

    protected $em;
    protected $class;
    protected $repository;

    /**
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    public function persistRedirect(Redirect $redirect)
    {
        $this->em->persist($redirect);
        $this->em->flush();
    }

}
