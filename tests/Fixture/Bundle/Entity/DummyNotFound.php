<?php

namespace Zenstruck\RedirectBundle\Tests\Fixture\Bundle\Entity;

use Zenstruck\RedirectBundle\Model\NotFound;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="not_founds")
 */
class DummyNotFound extends NotFound
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
