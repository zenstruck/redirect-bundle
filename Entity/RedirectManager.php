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
use Zenstruck\Bundle\RedirectBundle\Entity\Redirect;
use Symfony\Component\HttpFoundation\Request;
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

    /** @var string */
    protected $class;
    protected $options;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param array $options
     */
    public function __construct(EntityManager $em, array $options)
    {
        $class = $options['redirect_class'];

        $this->em = $em;
        $this->repository = $em->getRepository($class);
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

    public function getResponseForRequest(Request $request)
    {
        $source = $request->getRequestUri();

        $redirect = $this->findOneBySourceOrCreate($source, $this->options['allow_404_query_params']);

        if ($this->options['log_statistics']) {
            $this->logStatistics($redirect);
        }

        if ($redirect->is404Error()) {
            return null;
        }

        return new RedirectResponse($redirect->getDestination(), $redirect->getStatusCode());
    }

    public function logStatistics(Redirect $redirect, $save = true)
    {
        $redirect->increaseCount();
        $redirect->setLastAccessed(new \DateTime());

        if ($save) {
            $this->em->persist($redirect);
            $this->em->flush();
        }
    }

    /**
     * @param $source
     * @param bool $allowQueryParams
     * @return Redirect
     */
    public function findOneBySourceOrCreate($source, $allowQueryParams = true)
    {
        /** @var $temp Redirect */
        $temp = new $this->class;
        $temp->setSource($source);

        // try and find by full url (path + query)
        $redirect = $this->repository->findOneBySource($temp->getSource());

        if ($redirect) {
            return $redirect;
        }

        if ($allowQueryParams || !parse_url($temp->getSource(), PHP_URL_QUERY)) {
            return $temp;
        }

        // remove query string
        $temp->setSource(parse_url($temp->getSource(), PHP_URL_PATH));

        // try and find by path
        $redirect = $this->repository->findOneBySource($temp->getSource());

        if (!$redirect) {
            $redirect = $temp;
        }

        return $redirect;
    }

    /**
     * @param $source
     * @param $destination
     * @return Redirect
     */
    public function updateOrCreate($source, $destination)
    {
        $redirect = $this->findOneBySourceOrCreate($source);
        $redirect->setDestination($destination);

        $this->em->persist($redirect);
        $this->em->flush();

        return $redirect;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
