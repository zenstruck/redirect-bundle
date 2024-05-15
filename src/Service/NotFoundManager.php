<?php

/*
 * This file is part of the zenstruck/redirect-bundle package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\RedirectBundle\Service;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\RedirectBundle\Model\NotFound;
use Zenstruck\RedirectBundle\Model\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class NotFoundManager
{
    /**
     * @param string $class The NotFound class name
     */
    public function __construct(private string $class, private ObjectManager $om)
    {
    }

    public function createFromRequest(Request $request): NotFound
    {
        $notFound = new $this->class(
            $request->getPathInfo(),
            $request->getUri(),
            $request->server->get('HTTP_REFERER'),
        );

        $this->om->persist($notFound);
        $this->om->flush();

        return $notFound;
    }

    /**
     * Deletes NotFound entities for a Redirect's path.
     */
    public function removeForRedirect(Redirect $redirect): void
    {
        $notFounds = $this->om->getRepository($this->class)->findBy(['path' => $redirect->getSource()]);

        foreach ($notFounds as $notFound) {
            $this->om->remove($notFound);
        }

        $this->om->flush();
    }
}
