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
     * Returns array of results
     *
     * @param string $path
     */
    public function findBySource($path)
    {
        $qb = $this->getRepository()->createQueryBuilder('redirect');

        $qb->where('redirect.source = :path')
           ->orWhere('redirect.source LIKE :likestring')
           ->setParameter('path', $path)
           ->setParameter('likestring', $path.'#%');
   
        return $qb->getQuery()->execute();
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
