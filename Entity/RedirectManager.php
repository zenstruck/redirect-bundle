<?php

/*
 * This file is part of the ZenstruckRedirectBundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Bundle\RedirectBundle\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Zenstruck\Bundle\RedirectBundle\Entity\Redirect;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RedirectManager
{
    /** @var EntityManager **/
    protected $em;

    /** @var \Doctrine\ORM\EntityRepository **/
    protected $repository;

    /** @var EngineInterface **/
    protected $templating;

    /** @var string */
    protected $class;
    protected $options;

    /**
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, EngineInterface $templating, $options)
    {
        $class = $options['redirect_class'];

        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->templating = $templating;
        $this->options = $options;

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
    public function findBySource($path, $multiple = true)
    {
        $qb = $this->getRepository()->createQueryBuilder('redirect');

        $qb->where('redirect.source = :path')
           ->setParameter('path', $path);

        if ($multiple) {
            $qb->orWhere('redirect.source LIKE :likestring')
               ->setParameter('likestring', $path.'#%');
        }

        return $qb->getQuery()->execute();
    }

    public function updateOrCreate($source, $destination)
    {
        /** @var $temp Redirect */
        $temp = new $this->class;
        $temp->setSource($source);

        $redirect = $this->repository->findOneBySource($temp->getSource());

        if (!$redirect) {
            $redirect = $temp;
            $this->em->persist($redirect);
        }

        $redirect->setDestination($destination);

        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Determines correct redirect response
     *
     * @param string $source
     * @param string $baseUrl
     * @return Response|null
     */
    public function getResponse($source, $baseUrl = '', $multiple = true)
    {
        $redirects = $this->findBySource($source, $multiple);

        // more than 1 redirect found - render template to use javascript redirect
        if (count($redirects) > 1) {
            return $this->templating->renderResponse($this->options['redirect_template'],
                    array(
                        'baseUrl'   => $baseUrl
                    )
                );
        }

        $redirect = null;
        $response = null;

        // only 1 redirect was found
        if (count($redirects)) {
            $redirect = $redirects[0];
        }

        // no redirect was found
        if (!$redirect) {
            $class = $this->getClass();
            $redirect = new $class;
            $redirect->setSource($source);
        }

        // setup the response redirect if has destination
        if (!$redirect->is404Error()) {

            $destination = $redirect->getDestination();

            if (!$redirect->isDestinationAbsolute()) {
                $destination = $baseUrl . $destination;
            }

            $response = new RedirectResponse($destination, $redirect->getStatusCode());
        }

        $redirect->increaseCount();
        $redirect->setLastAccessed(new \DateTime());

        if (($this->options['log_statistics'] && !$redirect->is404Error()) || ($redirect->is404Error() && $this->options['log_404_errors'])) {
            $this->em->persist($redirect);
            $this->em->flush();
        }

        return $response;
    }

}
