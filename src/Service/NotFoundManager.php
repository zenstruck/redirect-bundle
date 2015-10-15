<?php

namespace Zenstruck\RedirectBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\RedirectBundle\Model\NotFound;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class NotFoundManager
{
    private $class;
    private $om;

    /**
     * @param string        $class The NotFound class name
     * @param ObjectManager $om
     */
    public function __construct($class, ObjectManager $om)
    {
        $this->class = $class;
        $this->om    = $om;
    }

    /**
     * @param Request $request
     *
     * @return NotFound
     */
    public function createFromRequest(Request $request)
    {
        $notFound = new $this->class(
            $request->getPathInfo(),
            $request->getUri(),
            $request->server->get('HTTP_REFERER')
        );

        $this->om->persist($notFound);
        $this->om->flush();

        return $notFound;
    }
}
