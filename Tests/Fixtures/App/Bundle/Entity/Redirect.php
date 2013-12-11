<?php

namespace Zenstruck\Bundle\RedirectBundle\Tests\Fixtures\App\Bundle\Entity;

use Zenstruck\Bundle\RedirectBundle\Entity\Redirect as BaseRedirect;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="redirect")
 */
class Redirect extends BaseRedirect
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
