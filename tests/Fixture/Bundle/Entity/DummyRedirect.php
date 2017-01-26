<?php

namespace Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zenstruck\RedirectBundle\Model\Redirect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="redirects")
 */
class DummyRedirect extends Redirect
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }
}
